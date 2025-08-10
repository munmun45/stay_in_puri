<?php
session_start();
require("../config/config.php");

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login");
    exit();
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
    $email = trim($_POST['email']);
    $google_page_link = !empty($_POST['google_page_link']) ? trim($_POST['google_page_link']) : null;
    $location = trim($_POST['location']);
    $logo_path = null;
    
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
    
    // Insert into database
    $sql = "INSERT INTO hotels (name, gst_no, mobile, email, google_page_link, location, logo) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $name, $gst_no, $mobile, $email, $google_page_link, $location, $logo_path);
    
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
    $email = trim($_POST['email']);
    $google_page_link = !empty($_POST['google_page_link']) ? trim($_POST['google_page_link']) : null;
    $location = trim($_POST['location']);
    
    // Validate required fields
    if (empty($name) || empty($mobile) || empty($email) || empty($location)) {
        echo "<script>alert('Please fill in all required fields.'); window.location.href = '../hotel-listing';</script>";
        exit();
    }
    
    // Get current logo path
    $current_logo_query = "SELECT logo FROM hotels WHERE id = ?";
    $stmt = $conn->prepare($current_logo_query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_logo = $result->fetch_assoc()['logo'];
    
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
            $sql = "UPDATE hotels SET name = ?, gst_no = ?, mobile = ?, email = ?, google_page_link = ?, location = ?, logo = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssi", $name, $gst_no, $mobile, $email, $google_page_link, $location, $logo_path, $id);
        } else {
            echo "<script>alert('Error uploading logo: " . $upload_result['message'] . "'); window.location.href = '../hotel-listing';</script>";
            exit();
        }
    } else {
        // Update without changing logo
        $sql = "UPDATE hotels SET name = ?, gst_no = ?, mobile = ?, email = ?, google_page_link = ?, location = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $gst_no, $mobile, $email, $google_page_link, $location, $id);
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
