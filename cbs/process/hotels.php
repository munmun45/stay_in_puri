<?php
session_start();
require("../config/config.php");

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login");
    exit();
}

// Handle generic document upload (image/PDF)
function uploadDocument($file) {
    $target_dir = "../uploads/hotel_docs/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $fileName = basename($file["name"]);
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ["jpg","jpeg","png","gif","pdf"];
    if (!in_array($ext, $allowed)) {
        return ["success" => false, "message" => "Only JPG, JPEG, PNG, GIF, PDF files are allowed."];
    }

    // If image, ensure it's actually an image
    if ($ext !== 'pdf') {
        $check = @getimagesize($file["tmp_name"]);
        if ($check === false) {
            return ["success" => false, "message" => "File is not a valid image."];
        }
    }

    // No explicit size limit here; rely on PHP's upload_max_filesize/post_max_size

    $newFileName = uniqid("doc_") . '.' . $ext;
    $target_file = $target_dir . $newFileName;
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "file_path" => "uploads/hotel_docs/" . $newFileName];
    }
    return ["success" => false, "message" => "There was an error uploading your file."];
}

// Handle toggle status
if (isset($_GET['toggle_status']) && isset($_GET['current'])) {
    $id = (int)$_GET['toggle_status'];
    $current_status = (int)$_GET['current'];
    $new_status = $current_status ? 0 : 1; // Toggle the status
    
    $update_sql = "UPDATE hotels SET is_active = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $new_status, $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Hotel status updated successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating hotel status: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: ../hotel-listing.php");
    exit();
}

// Handle file upload
function uploadImage($file) {
    $target_dir = "../uploads/hotel_logos/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $fileName = basename($file["name"]);
    $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $newFileName;
    
    // Check if image file is an actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["success" => false, "message" => "File is not an image."];
    }
    
    // Check file size (limit to 5MB)
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "File is too large. Max 5MB allowed."];
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        return ["success" => false, "message" => "Only JPG, JPEG, PNG & GIF files are allowed."];
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "file_path" => "uploads/hotel_logos/" . $newFileName];
    } else {
        return ["success" => false, "message" => "There was an error uploading your file."];
    }
}

// Add new hotel
if (isset($_POST['add_hotel'])) {
    $name = trim($_POST['name']);
    $gst_no = !empty($_POST['gst_no']) ? trim($_POST['gst_no']) : null;
    $mobile = trim($_POST['mobile']);
    $mobile2 = !empty($_POST['mobile2']) ? trim($_POST['mobile2']) : null;
    $mobile3 = !empty($_POST['mobile3']) ? trim($_POST['mobile3']) : null;
    $mobile4 = !empty($_POST['mobile4']) ? trim($_POST['mobile4']) : null;
    $email = trim($_POST['email']);
    $email2 = !empty($_POST['email2']) ? trim($_POST['email2']) : null;
    $google_page_link = !empty($_POST['google_page_link']) ? trim($_POST['google_page_link']) : null;
    $location = trim($_POST['location']);
    $address = !empty($_POST['address']) ? trim($_POST['address']) : null;
    $logo_path = null;
    $doc1_type = !empty($_POST['doc1_type']) ? trim($_POST['doc1_type']) : null;
    $doc2_type = !empty($_POST['doc2_type']) ? trim($_POST['doc2_type']) : null;
    $doc3_type = !empty($_POST['doc3_type']) ? trim($_POST['doc3_type']) : null;
    $doc4_type = !empty($_POST['doc4_type']) ? trim($_POST['doc4_type']) : null;
    $doc1_file_path = null;
    $doc2_file_path = null;
    $doc3_file_path = null;
    $doc4_file_path = null;
    
    // Validate required fields
    if (empty($name) || empty($mobile) || empty($email) || empty($location)) {
        echo "<script>alert('Please fill in all required fields.'); window.location.href = '../hotel-listing';</script>";
        exit();
    }
    
    // Handle logo upload if provided
    if (!empty($_FILES['logo']['name'])) {
        $upload_result = uploadImage($_FILES['logo']);
        if ($upload_result['success']) {
            $logo_path = $upload_result['file_path'];
        } else {
            echo "<script>alert('Error uploading logo: " . $upload_result['message'] . "'); window.location.href = '../hotel-listing';</script>";
            exit();
        }
    }

    // Handle documents upload if provided
    if (!empty($_FILES['doc1_file']['name'])) {
        $up = uploadDocument($_FILES['doc1_file']);
        if ($up['success']) { $doc1_file_path = $up['file_path']; }
        else { echo "<script>alert('Error uploading Document 1: " . $up['message'] . "'); window.location.href = '../hotel-listing';</script>"; exit(); }
    }
    if (!empty($_FILES['doc2_file']['name'])) {
        $up = uploadDocument($_FILES['doc2_file']);
        if ($up['success']) { $doc2_file_path = $up['file_path']; }
        else { echo "<script>alert('Error uploading Document 2: " . $up['message'] . "'); window.location.href = '../hotel-listing';</script>"; exit(); }
    }
    if (!empty($_FILES['doc3_file']['name'])) {
        $up = uploadDocument($_FILES['doc3_file']);
        if ($up['success']) { $doc3_file_path = $up['file_path']; }
        else { echo "<script>alert('Error uploading Document 3: " . $up['message'] . "'); window.location.href = '../hotel-listing';</script>"; exit(); }
    }
    if (!empty($_FILES['doc4_file']['name'])) {
        $up = uploadDocument($_FILES['doc4_file']);
        if ($up['success']) { $doc4_file_path = $up['file_path']; }
        else { echo "<script>alert('Error uploading Document 4: " . $up['message'] . "'); window.location.href = '../hotel-listing';</script>"; exit(); }
    }
    
    // Insert into database
    $sql = "INSERT INTO hotels 
            (name, gst_no, mobile, mobile2, mobile3, mobile4, email, email2, google_page_link, location, address, logo, 
             doc1_type, doc1_file, doc2_type, doc2_file, doc3_type, doc3_file, doc4_type, doc4_file, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "<script>alert('DB prepare failed (INSERT): " . addslashes($conn->error) . "'); window.location.href = '../hotel-listing';</script>";
        exit();
    }
    // 20 variables (is_active is literal 0 in SQL)
    $stmt->bind_param("ssssssssssssssssssss", 
        $name, $gst_no, $mobile, $mobile2, $mobile3, $mobile4, $email, $email2, $google_page_link, $location, $address, $logo_path,
        $doc1_type, $doc1_file_path, $doc2_type, $doc2_file_path, $doc3_type, $doc3_file_path, $doc4_type, $doc4_file_path
    );
    
    if ($stmt->execute()) {
        echo "<script>alert('Hotel added successfully.'); window.location.href = '../hotel-listing';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = '../hotel-listing';</script>";
    }
    exit();
}

// Update existing hotel
if (isset($_POST['update_hotel'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $gst_no = !empty($_POST['gst_no']) ? trim($_POST['gst_no']) : null;
    $mobile = trim($_POST['mobile']);
    $mobile2 = !empty($_POST['mobile2']) ? trim($_POST['mobile2']) : null;
    $mobile3 = !empty($_POST['mobile3']) ? trim($_POST['mobile3']) : null;
    $mobile4 = !empty($_POST['mobile4']) ? trim($_POST['mobile4']) : null;
    $email = trim($_POST['email']);
    $email2 = !empty($_POST['email2']) ? trim($_POST['email2']) : null;
    $google_page_link = !empty($_POST['google_page_link']) ? trim($_POST['google_page_link']) : null;
    $location = trim($_POST['location']);
    $address = !empty($_POST['address']) ? trim($_POST['address']) : null;
    $doc1_type = !empty($_POST['doc1_type']) ? trim($_POST['doc1_type']) : null;
    $doc2_type = !empty($_POST['doc2_type']) ? trim($_POST['doc2_type']) : null;
    
    // Validate required fields
    if (empty($name) || empty($mobile) || empty($email) || empty($location)) {
        echo "<script>alert('Please fill in all required fields.'); window.location.href = '../hotel-listing';</script>";
        exit();
    }
    
    // Get current stored files/fields
    $current_logo_query = "SELECT logo, doc1_file, doc2_file, doc3_file, doc4_file FROM hotels WHERE id = ?";
    $stmt = $conn->prepare($current_logo_query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current = $result->fetch_assoc();
    $current_logo = $current['logo'] ?? null;
    $current_doc1 = $current['doc1_file'] ?? null;
    $current_doc2 = $current['doc2_file'] ?? null;
    $current_doc3 = $current['doc3_file'] ?? null;
    $current_doc4 = $current['doc4_file'] ?? null;
    
    // Handle logo upload if provided
    if (!empty($_FILES['logo']['name'])) {
        $upload_result = uploadImage($_FILES['logo']);
        if ($upload_result['success']) {
            // Delete old logo if exists
            if (!empty($current_logo) && file_exists("../" . $current_logo)) {
                unlink("../" . $current_logo);
            }
            $logo_path = $upload_result['file_path'];
            
            // Update with new logo
            $sql = "UPDATE hotels SET name = ?, gst_no = ?, mobile = ?, mobile2 = ?, mobile3 = ?, mobile4 = ?, email = ?, email2 = ?, google_page_link = ?, location = ?, address = ?, logo = ?, doc1_type = ?, doc1_file = ?, doc2_type = ?, doc2_file = ?, doc3_type = ?, doc3_file = ?, doc4_type = ?, doc4_file = ? WHERE id = ?";
            // Before binding, handle doc uploads if any
            $doc1_file_path = $current_doc1;
            $doc2_file_path = $current_doc2;
            $doc3_file_path = $current_doc3;
            $doc4_file_path = $current_doc4;
            if (!empty($_FILES['doc1_file']['name'])) {
                $up = uploadDocument($_FILES['doc1_file']);
                if ($up['success']) {
                    if (!empty($current_doc1) && file_exists("../" . $current_doc1)) { unlink("../" . $current_doc1); }
                    $doc1_file_path = $up['file_path'];
                } else { echo "<script>alert('Error uploading Document 1: " . $up['message'] . "'); window.location.href = '../hotel-listing';</script>"; exit(); }
            }
            if (!empty($_FILES['doc2_file']['name'])) {
                $up = uploadDocument($_FILES['doc2_file']);
                if ($up['success']) {
                    if (!empty($current_doc2) && file_exists("../" . $current_doc2)) { unlink("../" . $current_doc2); }
                    $doc2_file_path = $up['file_path'];
                } else { echo "<script>alert('Error uploading Document 2: " . $up['message'] . "'); window.location.href = '../hotel-listing';</script>"; exit(); }
            }
            if (!empty($_FILES['doc3_file']['name'])) {
                $up = uploadDocument($_FILES['doc3_file']);
                if ($up['success']) {
                    if (!empty($current_doc3) && file_exists("../" . $current_doc3)) { unlink("../" . $current_doc3); }
                    $doc3_file_path = $up['file_path'];
                } else { echo "<script>alert('Error uploading Document 3: " . $up['message'] . "'); window.location.href = '../hotel-listing';</script>"; exit(); }
            }
            if (!empty($_FILES['doc4_file']['name'])) {
                $up = uploadDocument($_FILES['doc4_file']);
                if ($up['success']) {
                    if (!empty($current_doc4) && file_exists("../" . $current_doc4)) { unlink("../" . $current_doc4); }
                    $doc4_file_path = $up['file_path'];
                } else { echo "<script>alert('Error uploading Document 4: " . $up['message'] . "'); window.location.href = '../hotel-listing';</script>"; exit(); }
            }
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                echo "<script>alert('DB prepare failed (UPDATE with logo): " . addslashes($conn->error) . "'); window.location.href = '../hotel-listing';</script>";
                exit();
            }
            $stmt->bind_param(
                "ssssssssssssssssssssi",
                $name, $gst_no, $mobile, $mobile2, $mobile3, $mobile4, $email, $email2,
                $google_page_link, $location, $address, $logo_path,
                $doc1_type, $doc1_file_path, $doc2_type, $doc2_file_path, $doc3_type, $doc3_file_path, $doc4_type, $doc4_file_path,
                $id
            );
        } else {
            echo "<script>alert('Error uploading logo: " . $upload_result['message'] . "'); window.location.href = '../hotel-listing';</script>";
            exit();
        }
    } else {
        // Update without changing logo; also handle documents similarly
        $doc1_file_path = $current_doc1;
        $doc2_file_path = $current_doc2;
        $doc3_file_path = $current_doc3;
        $doc4_file_path = $current_doc4;
        if (!empty($_FILES['doc1_file']['name'])) {
            $up = uploadDocument($_FILES['doc1_file']);
            if ($up['success']) {
                if (!empty($current_doc1) && file_exists("../" . $current_doc1)) { unlink("../" . $current_doc1); }
                $doc1_file_path = $up['file_path'];
            } else { echo "<script>alert('Error uploading Document 1: " . $up['message'] . "'); window.location.href = '../hotel-listing';</script>"; exit(); }
        }
        if (!empty($_FILES['doc2_file']['name'])) {
            $up = uploadDocument($_FILES['doc2_file']);
            if ($up['success']) {
                if (!empty($current_doc2) && file_exists("../" . $current_doc2)) { unlink("../" . $current_doc2); }
                $doc2_file_path = $up['file_path'];
            } else { echo "<script>alert('Error uploading Document 2: " . $up['message'] . "'); window.location.href = '../hotel-listing';</script>"; exit(); }
        }
        if (!empty($_FILES['doc3_file']['name'])) {
            $up = uploadDocument($_FILES['doc3_file']);
            if ($up['success']) {
                if (!empty($current_doc3) && file_exists("../" . $current_doc3)) { unlink("../" . $current_doc3); }
                $doc3_file_path = $up['file_path'];
            } else { echo "<script>alert('Error uploading Document 3: " . $up['message'] . "'); window.location.href = '../hotel-listing';</script>"; exit(); }
        }
        if (!empty($_FILES['doc4_file']['name'])) {
            $up = uploadDocument($_FILES['doc4_file']);
            if ($up['success']) {
                if (!empty($current_doc4) && file_exists("../" . $current_doc4)) { unlink("../" . $current_doc4); }
                $doc4_file_path = $up['file_path'];
            } else { echo "<script>alert('Error uploading Document 4: " . $up['message'] . "'); window.location.href = '../hotel-listing';</script>"; exit(); }
        }
        $sql = "UPDATE hotels SET name = ?, gst_no = ?, mobile = ?, mobile2 = ?, mobile3 = ?, mobile4 = ?, email = ?, email2 = ?, google_page_link = ?, location = ?, address = ?, doc1_type = ?, doc1_file = ?, doc2_type = ?, doc2_file = ?, doc3_type = ?, doc3_file = ?, doc4_type = ?, doc4_file = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo "<script>alert('DB prepare failed (UPDATE): " . addslashes($conn->error) . "'); window.location.href = '../hotel-listing';</script>";
            exit();
        }
        $stmt->bind_param(
            "sssssssssssssssssssi",
            $name, $gst_no, $mobile, $mobile2, $mobile3, $mobile4, $email, $email2,
            $google_page_link, $location, $address, $doc1_type, $doc1_file_path, $doc2_type, $doc2_file_path, $doc3_type, $doc3_file_path, $doc4_type, $doc4_file_path, $id
        );
    }
    
    if ($stmt->execute()) {
        echo "<script>alert('Hotel updated successfully.'); window.location.href = '../hotel-listing';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = '../hotel-listing';</script>";
    }
    exit();
}

// Delete hotel
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get logo path before deleting
    $logo_query = "SELECT logo FROM hotels WHERE id = ?";
    $stmt = $conn->prepare($logo_query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $logo_path = $result->fetch_assoc()['logo'];
    
    // Delete from database
    $sql = "DELETE FROM hotels WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Delete logo file if exists
        if (!empty($logo_path) && file_exists("../" . $logo_path)) {
            unlink("../" . $logo_path);
        }
        echo "<script>alert('Hotel deleted successfully.'); window.location.href = '../hotel-listing';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = '../hotel-listing';</script>";
    }
    exit();
}
?>
