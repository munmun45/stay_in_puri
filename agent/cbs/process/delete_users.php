<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Database connection
require_once '../config/database.php';

// Check if user ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "User ID not provided";
    header("Location: ../users.php");
    exit;
}

// Get user ID
$id = (int)$_GET['id'];

// Check if user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "User not found";
    header("Location: ../users.php");
    exit;
}
$stmt->close();

// Delete user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success'] = "User deleted successfully";
} else {
    $_SESSION['error'] = "Error deleting user: " . $conn->error;
}

header("Location: ../users.php");
exit;
?>
