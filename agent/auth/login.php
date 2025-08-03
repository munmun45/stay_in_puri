<?php
// Start session
session_start();

// Include database connection
require_once '../cbs/config/database.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $error = '';

    // Check if login is by phone
    $loginByPhone = !empty($phone);
    
    // Validate input based on login method
    if ($loginByPhone) {
        if (empty($phone) || empty($password)) {
            $_SESSION['error'] = "Please enter both phone number and password";
            header("Location: ../index.php");
            exit;
        }
    } else {
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = "Please enter both username and password";
            header("Location: ../index.php");
            exit;
        }
    }
    // Prepare SQL based on login method
    if ($loginByPhone) {
        $sql = "SELECT id, username, password, role, user_type, phone, status FROM users WHERE phone = ?";
        $loginParam = trim($_POST['phone']);
    } else {
        $sql = "SELECT id, username, password, role, user_type, phone, status FROM users WHERE username = ?";
        $loginParam = $username;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loginParam);
    $stmt->execute();
    $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Check if account is active
            if ($user['status'] !== 'active') {
                $_SESSION['error'] = "Your account is not active. Please contact support.";
                header("Location: ../index.php");
                exit;
            }
            
            // Verify password (plain text comparison for now, consider using password_verify() for hashed passwords)
            if ($password === $user['password']) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['success'] = "Login successful! Welcome " . $user['username'];
                
                // Redirect to dashboard (same for all roles currently)
                header("Location: ../cbs/dashboard.php");
                exit;
            } else {
                $_SESSION['error'] = "Invalid " . ($loginByPhone ? 'phone number or ' : '') . "password";
                header("Location: ../index.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Invalid " . ($loginByPhone ? 'phone number' : 'username or password');
            header("Location: ../index.php");
            exit;
        }
        
        $stmt->close();
    }

?>
