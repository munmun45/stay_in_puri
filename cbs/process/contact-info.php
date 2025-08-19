<?php
session_start();
require_once __DIR__ . '/../config/config.php';

// Require login (align with other process files)
if (!isset($_SESSION['username'])) {
    header('Location: ../login');
    exit();
}

// Ensure table exists
$conn->query("CREATE TABLE IF NOT EXISTS contact_info (
  id INT PRIMARY KEY,
  phone1 VARCHAR(50) DEFAULT NULL,
  phone2 VARCHAR(50) DEFAULT NULL,
  email1 VARCHAR(100) DEFAULT NULL,
  email2 VARCHAR(100) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  google_map TEXT DEFAULT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if (isset($_POST['save_contact'])) {
    $phone1 = trim($_POST['phone1'] ?? '');
    $phone2 = trim($_POST['phone2'] ?? '');
    $email1 = trim($_POST['email1'] ?? '');
    $email2 = trim($_POST['email2'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $google_map = trim($_POST['google_map'] ?? '');

    // Upsert single row with id=1
    // Try update first
    $stmt = $conn->prepare("UPDATE contact_info SET phone1=?, phone2=?, email1=?, email2=?, address=?, google_map=? WHERE id=1");
    $stmt->bind_param('ssssss', $phone1, $phone2, $email1, $email2, $address, $google_map);
    if ($stmt->execute()) {
        if ($stmt->affected_rows === 0) {
            // No row existed, insert
            $stmt2 = $conn->prepare("INSERT INTO contact_info (id, phone1, phone2, email1, email2, address, google_map) VALUES (1, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param('ssssss', $phone1, $phone2, $email1, $email2, $address, $google_map);
            if ($stmt2->execute()) {
                echo "<script>alert('Contact info saved.'); window.location.href = '../contact-info';</script>";
                exit();
            } else {
                echo "<script>alert('Error: " . addslashes($stmt2->error) . "'); window.location.href = '../contact-info';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Contact info updated.'); window.location.href = '../contact-info';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "'); window.location.href = '../contact-info';</script>";
        exit();
    }
}

// Fallback
header('Location: ../contact-info');
exit();
