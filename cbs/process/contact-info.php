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
  facebook VARCHAR(255) DEFAULT NULL,
  twitter VARCHAR(255) DEFAULT NULL,
  instagram VARCHAR(255) DEFAULT NULL,
  youtube VARCHAR(255) DEFAULT NULL,
  whatsapp VARCHAR(255) DEFAULT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Ensure columns exist even if table existed from older schema
try {
    $cols = [];
    if ($res = $conn->query("SHOW COLUMNS FROM contact_info")) {
        while ($row = $res->fetch_assoc()) { $cols[strtolower($row['Field'])] = true; }
        $res->free();
    }
    $toAdd = [];
    if (!isset($cols['facebook']))  { $toAdd[] = "ADD COLUMN facebook VARCHAR(255) DEFAULT NULL"; }
    if (!isset($cols['twitter']))   { $toAdd[] = "ADD COLUMN twitter VARCHAR(255) DEFAULT NULL"; }
    if (!isset($cols['instagram'])) { $toAdd[] = "ADD COLUMN instagram VARCHAR(255) DEFAULT NULL"; }
    if (!isset($cols['youtube']))   { $toAdd[] = "ADD COLUMN youtube VARCHAR(255) DEFAULT NULL"; }
    if (!isset($cols['whatsapp']))  { $toAdd[] = "ADD COLUMN whatsapp VARCHAR(255) DEFAULT NULL"; }
    if (!empty($toAdd)) {
        $conn->query("ALTER TABLE contact_info " . implode(', ', $toAdd));
    }
} catch (Throwable $e) {
    // ignore; worst case, prepare will still fail and show error for debugging
}

if (isset($_POST['save_contact'])) {
    $phone1 = trim($_POST['phone1'] ?? '');
    $phone2 = trim($_POST['phone2'] ?? '');
    $email1 = trim($_POST['email1'] ?? '');
    $email2 = trim($_POST['email2'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $google_map = trim($_POST['google_map'] ?? '');
    $facebook = trim($_POST['facebook'] ?? '');
    $twitter = trim($_POST['twitter'] ?? '');
    $instagram = trim($_POST['instagram'] ?? '');
    $youtube = trim($_POST['youtube'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');

    // Upsert single row with id=1 using one statement to avoid duplicate key errors
    $sql = "INSERT INTO contact_info (id, phone1, phone2, email1, email2, address, google_map, facebook, twitter, instagram, youtube, whatsapp)
            VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
              phone1 = ?,
              phone2 = ?,
              email1 = ?,
              email2 = ?,
              address = ?,
              google_map = ?,
              facebook = ?,
              twitter = ?,
              instagram = ?,
              youtube = ?,
              whatsapp = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "<script>alert('DB error (upsert prepare): " . addslashes($conn->error) . "'); window.location.href = '../contact-info';</script>";
        exit();
    }
    // 22 params: 11 for insert + 11 for update
    $stmt->bind_param(
        'ssssssssssssssssssssss',
        $phone1, $phone2, $email1, $email2, $address, $google_map, $facebook, $twitter, $instagram, $youtube, $whatsapp,
        $phone1, $phone2, $email1, $email2, $address, $google_map, $facebook, $twitter, $instagram, $youtube, $whatsapp
    );
    if ($stmt->execute()) {
        echo "<script>alert('Contact info saved.'); window.location.href = '../contact-info';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "'); window.location.href = '../contact-info';</script>";
        exit();
    }
}

// Fallback
header('Location: ../contact-info');
exit();
