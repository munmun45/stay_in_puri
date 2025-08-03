<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the record ID
    $id = (int)$_POST['id'];
    
    // Validate and sanitize input
    $user_id = trim($_POST['user_id'] ?? '');
    $id_number = trim($_POST['id_number'] ?? '');
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_number1 = trim($_POST['customer_number1'] ?? '');
    $customer_number2 = trim($_POST['customer_number2'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $current_address = trim($_POST['current_address'] ?? '');
    $permanent_address = trim($_POST['permanent_address'] ?? '');
    $postal_pin = trim($_POST['postal_pin'] ?? '');
    $nomini_name = trim($_POST['nomini_name'] ?? '');
    $nomini_dob = !empty($_POST['nomini_dob']) ? $_POST['nomini_dob'] : null;
    $company_name = trim($_POST['company_name'] ?? '');
    $gst_number = trim($_POST['gst_number'] ?? '');
    $vehicle_number = trim($_POST['vehicle_number'] ?? '');
    $make_model = trim($_POST['make_model'] ?? '');
    $reg_date = !empty($_POST['reg_date']) ? $_POST['reg_date'] : null;
    $policy_no = trim($_POST['policy_no'] ?? '');
    $policy_company = trim($_POST['policy_company'] ?? '');
    $policy_start_date = !empty($_POST['policy_start_date']) ? $_POST['policy_start_date'] : null;
    $policy_end_date = !empty($_POST['policy_end_date']) ? $_POST['policy_end_date'] : null;
    $gross_amount = !empty($_POST['gross_amount']) ? (float)$_POST['gross_amount'] : 0.00;
    $net_amount = !empty($_POST['net_amount']) ? (float)$_POST['net_amount'] : 0.00;
    $commission = !empty($_POST['commission']) ? (float)$_POST['commission'] : 0.00;


    // Handle file upload if a new file is provided
    $documentPath = '';
    $updateDocument = false;
    
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $fileType = $_FILES['document']['type'];
        $fileSize = $_FILES['document']['size'];
        $fileName = $_FILES['document']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['error'] = "Only JPG, JPEG, PNG & PDF files are allowed.";
            header("Location: ../vehicles.php");
            exit;
        }
        
        if ($fileSize > $maxFileSize) {
            $_SESSION['error'] = "File is too large. Maximum size allowed is 5MB.";
            header("Location: ../vehicles.php");
            exit;
        }
        
        // Create uploads directory if it doesn't exist
        $uploadDir = '../uploads/vehicles/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $newFileName = uniqid('doc_') . '.' . $fileExt;
        $targetPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($_FILES['document']['tmp_name'], $targetPath)) {
            $documentPath = 'uploads/vehicles/' . $newFileName;
            $updateDocument = true;
        } else {
            $_SESSION['error'] = "Error uploading file. Please try again.";
            header("Location: ../vehicles.php");
            exit;
        }
    }
    
    // Get current document path if exists
    $currentDocumentPath = '';
    $getDocStmt = $conn->prepare("SELECT document_path FROM vehicles WHERE id = ?");
    $getDocStmt->bind_param("i", $id);
    $getDocStmt->execute();
    $getDocStmt->bind_result($currentDocumentPath);
    $getDocStmt->fetch();
    $getDocStmt->close();

    // Validate required fields
    if (empty($customer_name) || empty($customer_number1) || empty($vehicle_number)) {
        $_SESSION['error'] = "Please fill in all required fields (Customer Name, Customer Number, and Vehicle Number are required)";
        header("Location: ../vehicles.php");
        exit;
    }
    
    if (!empty($customer_email) && !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address";
        header("Location: ../vehicles.php");
        exit;
    }

    // Check if policy number is already used by another record if provided
    if (!empty($policy_no)) {
        $stmt = $conn->prepare("SELECT id FROM vehicles WHERE policy_no = ? AND id != ?");
        $stmt->bind_param("si", $policy_no, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['error'] = "A record with this policy number already exists";
            header("Location: ../vehicles.php");
            exit;
        }
    }

    // Start transaction for the entire update process
    $conn->begin_transaction();
    
    try {
        // First, handle the document upload if a new file was provided
        if ($updateDocument) {
            // Delete old document if it exists
            if (!empty($currentDocumentPath) && file_exists('../' . $currentDocumentPath)) {
                unlink('../' . $currentDocumentPath);
            }
            
            // Update the document path in the database
            $updateDocStmt = $conn->prepare("UPDATE vehicles SET document_path = ? WHERE id = ?");
            $updateDocStmt->bind_param("si", $documentPath, $id);
            if (!$updateDocStmt->execute()) {
                throw new Exception("Error updating document path: " . $conn->error);
            }
            $updateDocStmt->close();
        }
        
        // Update the vehicle insurance record
        $stmt = $conn->prepare("UPDATE vehicles SET 
            id_number = ?, 
            customer_name = ?, 
            customer_number1 = ?, 
            customer_number2 = ?, 
            customer_email = ?, 
            current_address = ?, 
            permanent_address = ?, 
            postal_pin = ?, 
            nomini_name = ?, 
            nomini_dob = ?, 
            company_name = ?, 
            gst_number = ?, 
            vehicle_number = ?, 
            make_model = ?, 
            reg_date = ?, 
            policy_no = ?, 
            policy_company = ?, 
            policy_start_date = ?, 
            policy_end_date = ?, 
            gross_amount = ?, 
            net_amount = ?, 
            commission = ?,
            user_id = ?
            WHERE id = ?");
        
        $stmt->bind_param(
            "ssssssssssssssssssssssii",
            $id_number, 
            $customer_name, 
            $customer_number1, 
            $customer_number2, 
            $customer_email,
            $current_address, 
            $permanent_address, 
            $postal_pin, 
            $nomini_name, 
            $nomini_dob,
            $company_name, 
            $gst_number, 
            $vehicle_number, 
            $make_model, 
            $reg_date,
            $policy_no, 
            $policy_company, 
            $policy_start_date, 
            $policy_end_date,
            $gross_amount, 
            $net_amount, 
            $commission,
            $user_id,
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Error updating vehicle record: " . $stmt->error);
        }
        $stmt->close();
        
        // Commit the transaction
        $conn->commit();
        $_SESSION['success'] = "Vehicle insurance updated successfully";
            
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        
        // Clean up uploaded file if it was created
        if ($updateDocument && !empty($documentPath) && file_exists('../' . $documentPath)) {
            @unlink('../' . $documentPath);
        }
        
        error_log("Error in update_vehicles.php: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred while updating the vehicle record. Please try again.";
    }
        
        header("Location: ../vehicles.php");
        exit;
}
?>
