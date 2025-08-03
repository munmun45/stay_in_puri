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

// Check if ID is provided
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid sponsor ID']);
    exit();
}

$id = (int)$_POST['id'];
$userId = $_SESSION['user_id'];

try {
    // Begin transaction
    $conn->begin_transaction();
    
    // Get file path before deleting
    $stmt = $conn->prepare("SELECT image_path FROM sponsors WHERE id = ? AND uploaded_by = ? FOR UPDATE");
    $stmt->bind_param("ii", $id, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        throw new Exception('Sponsor image not found or access denied');
    }
    
    $file = $result->fetch_assoc();
    $filePath = '../' . $file['image_path'];
    
    // Delete from database
    $deleteStmt = $conn->prepare("DELETE FROM sponsors WHERE id = ? AND uploaded_by = ?");
    $deleteStmt->bind_param("ii", $id, $userId);
    
    if (!$deleteStmt->execute()) {
        throw new Exception('Failed to delete sponsor from database');
    }
    
    // Delete the actual file
    if (file_exists($filePath)) {
        if (!unlink($filePath)) {
            throw new Exception('Failed to delete sponsor image file');
        }
    }
    
    // If we got here, everything is good
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Sponsor image deleted successfully'
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    
} finally {
    // Close statements if they exist
    if (isset($stmt)) $stmt->close();
    if (isset($deleteStmt)) $deleteStmt->close();
    $conn->close();
}
