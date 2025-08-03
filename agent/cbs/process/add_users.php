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
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['mobile']);  // Input field is still 'mobile' in the form
    $email = trim($_POST['email']);
    $address = trim($_POST['address'] ?? '');
    $password = trim($_POST['password']);
    $status = trim($_POST['status']);
    $notes = trim($_POST['notes'] ?? '');
    
    // Generate username from full name and phone
    $name_part = preg_replace('/\s+/', '', $full_name);
    $name_part = strtolower(substr($name_part, 0, 4));
    $phone_part = preg_replace('/\D/', '', $phone);
    $phone_part = substr($phone_part, -4);
    $phone_part = str_pad($phone_part, 4, '0', STR_PAD_LEFT);
    $username = $name_part . $phone_part;

    // Get user type
    $user_type = trim($_POST['user_type']);

    
    
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

    // Check if phone number already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Phone number already exists";
        header("Location: ../users.php");
        exit;
    }
    $stmt->close();

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Username already exists";
        header("Location: ../users.php");
        exit;
    }
    $stmt->close();

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already exists";
        header("Location: ../users.php");
        exit;
    }
    $stmt->close();

    // Hash password
    $hashed_password = $password;

    // Set timezone to Indian Standard Time (IST)
    date_default_timezone_set('Asia/Kolkata');
    $created_at = date('Y-m-d H:i:s');

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, full_name, phone, address, status, notes, user_type, created_at) VALUES (?, ?, ?, 'user', ?, ?, ?, ?, ?, ?,?)");
    $stmt->bind_param("ssssssssss", $username, $email, $hashed_password, $full_name, $phone, $address, $status, $notes, $user_type, $created_at);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "User added successfully";
        header("Location: ../users.php");
        exit;
    } else {
        $_SESSION['error'] = "Error adding user: " . $conn->error;
        header("Location: ../users.php");
        exit;
    }
}
?>
