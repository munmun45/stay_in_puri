<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'primary') {
    header("Location: ../index.php");
    exit;
}

// Database connection
require_once '../config/database.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $id = (int)$_POST['id'];
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address'] ?? '');
    $password = trim($_POST['password']);
    $status = trim($_POST['status']);
    $notes = trim($_POST['notes'] ?? '');

    // Get user type
    $user_type = trim($_POST['user_type']);

    // Validate required fields
    if (empty($full_name) || empty($username) || empty($email) || empty($phone) || empty($status) || empty($user_type)) {
        $_SESSION['error'] = "Please fill in all required fields";
        header("Location: ../users.php");
        exit;
    }
    
    // Validate user type
    if (!in_array($user_type, ['primary', 'secondary'])) {
        $_SESSION['error'] = "Invalid user type";
        header("Location: ../users.php");
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address";
        header("Location: ../users.php");
        exit;
    }

    // Check if username already exists (excluding current user)
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $username, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Username already exists";
        header("Location: ../users.php");
        exit;
    }
    
    // Check if phone number already exists (excluding current user)
    $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
    $stmt->bind_param("si", $phone, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Phone number already exists";
        header("Location: ../users.php");
        exit;
    }
    $stmt->close();

    // Check if email already exists (excluding current user)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already exists";
        header("Location: ../users.php");
        exit;
    }
    $stmt->close();

    // Update user in database
    if (!empty($password)) {
        // If password is being updated
        $hashed_password = $password;
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, phone = ?, address = ?, password = ?, status = ?, notes = ?, user_type = ? WHERE id = ?");
        $stmt->bind_param("sssssssssi", $full_name, $username, $email, $phone, $address, $hashed_password, $status, $notes, $user_type, $id);
    } else {
        // If password is not being updated
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, phone = ?, address = ?, status = ?, notes = ?, user_type = ? WHERE id = ?");
        $stmt->bind_param("ssssssssi", $full_name, $username, $email, $phone, $address, $status, $notes, $user_type, $id);
    }
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "User updated successfully";
        header("Location: ../users.php");
        exit;
    } else {
        $_SESSION['error'] = "Error updating user: " . $conn->error;
        header("Location: ../users.php");
        exit;
    }
}
?>
