<?php
session_start();

// Check if user is logged in and is primary user
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'primary') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Database connection
require_once '../config/database.php';

header('Content-Type: application/json');

// Check if files were uploaded
if (!isset($_FILES['sponsor_images'])) {
    echo json_encode(['success' => false, 'message' => 'No files uploaded']);
    exit();
}

$uploadDir = '../uploads/sponsors/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
$maxFileSize = 5 * 1024 * 1024; // 5MB
$uploadedFiles = [];
$errors = [];

// Create directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Process each file
foreach ($_FILES['sponsor_images']['tmp_name'] as $key => $tmpName) {
    $fileName = $_FILES['sponsor_images']['name'][$key];
    $fileTmpName = $_FILES['sponsor_images']['tmp_name'][$key];
    $fileSize = $_FILES['sponsor_images']['size'][$key];
    $fileType = $_FILES['sponsor_images']['type'][$key];
    $fileError = $_FILES['sponsor_images']['error'][$key];
    
    // Skip if there was an error or no file was uploaded
    if ($fileError !== UPLOAD_ERR_OK || !is_uploaded_file($fileTmpName)) {
        $errors[] = "Error uploading $fileName";
        continue;
    }
    
    // Validate file type
    if (!in_array($fileType, $allowedTypes)) {
        $errors[] = "$fileName: Invalid file type. Only JPG, PNG, and WebP images are allowed.";
        continue;
    }
    
    // Validate file size
    if ($fileSize > $maxFileSize) {
        $errors[] = "$fileName: File is too large. Maximum size is 5MB.";
        continue;
    }
    
    // Generate unique filename
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
    $newFileName = uniqid('sponsor_') . '.' . $fileExt;
    $targetPath = $uploadDir . $newFileName;
    
    // Move uploaded file
    if (move_uploaded_file($fileTmpName, $targetPath)) {
        // Save to database
        $stmt = $conn->prepare("INSERT INTO sponsors (image_path, original_name, file_size, file_type, uploaded_by) VALUES (?, ?, ?, ?, ?)");
        $relativePath = 'uploads/sponsors/' . $newFileName;
        $stmt->bind_param("ssisi", $relativePath, $fileName, $fileSize, $fileType, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $uploadedFiles[] = [
                'id' => $stmt->insert_id,
                'name' => $fileName,
                'path' => $relativePath,
                'size' => $fileSize,
                'uploaded_at' => date('Y-m-d H:i:s')
            ];
        } else {
            $errors[] = "Failed to save $fileName to database";
            unlink($targetPath); // Delete the file if database insert fails
        }
        $stmt->close();
    } else {
        $errors[] = "Failed to upload $fileName";
    }
}

if (!empty($uploadedFiles)) {
    echo json_encode([
        'success' => true,
        'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
        'files' => $uploadedFiles,
        'errors' => $errors
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'No files were uploaded',
        'errors' => $errors
    ]);
}
