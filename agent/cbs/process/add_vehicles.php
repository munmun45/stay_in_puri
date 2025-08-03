<?php
session_start();
require_once '../config/database.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Handle file upload
    $documentPath = '';
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
        } else {
            $_SESSION['error'] = "Error uploading file. Please try again.";
            header("Location: ../vehicles.php");
            exit;
        }
    }

    // Validate required fields
    if (empty($customer_name) || empty($customer_number1) || empty($vehicle_number)) {
        // Delete uploaded file if validation fails
        if (!empty($documentPath) && file_exists('../' . $documentPath)) {
            unlink('../' . $documentPath);
        }
        $_SESSION['error'] = "Please fill in all required fields (Customer Name, Customer Number, and Vehicle Number are required)";
    } elseif (!empty($customer_email) && !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        // Delete uploaded file if email is invalid
        if (!empty($documentPath) && file_exists('../' . $documentPath)) {
            unlink('../' . $documentPath);
        }
        $_SESSION['error'] = "Please enter a valid email address";
    } else {
        // Check if policy number already exists if provided
        if (!empty($policy_no)) {
            $stmt = $conn->prepare("SELECT id FROM vehicles WHERE policy_no = ?");
            $stmt->bind_param("s", $policy_no);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $_SESSION['error'] = "A record with this policy number already exists";
                header("Location: ../vehicles.php");
                exit;
            }
        }

        // Start transaction
        $conn->begin_transaction();

        $created_by = $_SESSION['user_id'];


        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        
        try {
            // Insert vehicle insurance record
            $stmt = $conn->prepare("INSERT INTO vehicles (id_number, customer_name, customer_number1, customer_number2, customer_email, 
                current_address, permanent_address, postal_pin, nomini_name, nomini_dob, company_name, gst_number, 
                vehicle_number, make_model, reg_date, policy_no, policy_company, policy_start_date, policy_end_date, 
                gross_amount, net_amount, commission, document_path, created_at, user_id, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
            // Type string breakdown:
            // 19 strings (s) + 3 doubles (d) + 1 string (s) + 1 integer (i) = 24 parameters
            $types = "sssssssssssssssssssdddssii";
            
            $stmt->bind_param($types, 
                $id_number, $customer_name, $customer_number1, $customer_number2, $customer_email, 
                $current_address, $permanent_address, $postal_pin, $nomini_name, $nomini_dob, 
                $company_name, $gst_number, $vehicle_number, $make_model, $reg_date, 
                $policy_no, $policy_company, $policy_start_date, $policy_end_date, 
                $gross_amount, $net_amount, $commission, $documentPath, $created_at, $user_id, $created_by
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Error inserting vehicle record: " . $stmt->error);
            }
            
            // Get the inserted vehicle ID
            $vehicle_id = $stmt->insert_id;
            $stmt->close();
            
            // Commit the transaction
            $conn->commit();
            $_SESSION['success'] = "Vehicle insurance added successfully!";
            
        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
        
        header("Location: ../vehicles.php");
        exit;
t->close();
    }
}
