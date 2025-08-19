<?php
session_start();
require_once "../config/config.php";

// Ensure ads table exists
$conn->query("CREATE TABLE IF NOT EXISTS ads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hotel_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_ads_hotel FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

function ensureUploadDir($subdir) {
    $dir = dirname(__DIR__) . "/uploads/" . $subdir . "/";
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        @file_put_contents($dir . 'index.html', '');
    }
    return $dir;
}

function saveBase64Image($base64, $subdir = 'ads') {
    if (strpos($base64, 'data:image') !== 0) return [false, 'Invalid image data'];
    [$meta, $data] = explode(',', $base64, 2);
    if (!$data) return [false, 'Invalid image data'];
    $ext = 'png';
    if (strpos($meta, 'image/jpeg') !== false) $ext = 'jpg';
    elseif (strpos($meta, 'image/webp') !== false) $ext = 'webp';
    $bin = base64_decode($data);
    if ($bin === false) return [false, 'Failed to decode image'];

    $dir = ensureUploadDir($subdir);
    $name = uniqid('ad_', true) . '.' . $ext;
    $path = $dir . $name;
    if (file_put_contents($path, $bin) === false) return [false, 'Failed to save image'];
    $relative = "uploads/{$subdir}/" . $name;
    return [true, $relative];
}

// Require login
if (!isset($_SESSION['username'])) {
    header("Location: ../login");
    exit();
}

// Add ad
if (isset($_POST['add_ad'])) {
    $title = trim($_POST['title'] ?? '');
    $hotel_id = (int)($_POST['hotel_id'] ?? 0);
    $cropped = $_POST['cropped_image'] ?? '';

    if ($title === '' || $cropped === '' || $hotel_id <= 0) {
        echo "<script>alert('Hotel, Title and image are required.'); window.location.href = '../ads-listing';</script>";
        exit();
    }

    [$ok, $res] = saveBase64Image($cropped, 'ads');
    if (!$ok) {
        echo "<script>alert('" . addslashes($res) . "'); window.location.href = '../ads-listing';</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO ads (hotel_id, title, image_path) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $hotel_id, $title, $res);
    if ($stmt->execute()) {
        echo "<script>alert('Ad added successfully.'); window.location.href = '../ads-listing';</script>";
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "'); window.location.href = '../ads-listing';</script>";
    }
    exit();
}

// Update ad
if (isset($_POST['update_ad'])) {
    $id = (int)($_POST['id'] ?? 0);
    $hotel_id = (int)($_POST['hotel_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $cropped = $_POST['cropped_image'] ?? '';

    if ($id <= 0 || $title === '' || $hotel_id <= 0) {
        echo "<script>alert('Invalid input.'); window.location.href = '../ads-listing';</script>";
        exit();
    }

    // Get current image path
    $current = null;
    $resq = $conn->query("SELECT image_path FROM ads WHERE id = " . $id);
    if ($resq && $resq->num_rows > 0) { $current = $resq->fetch_assoc()['image_path']; }

    $image_path = $current;
    $new_saved = false;
    if ($cropped !== '') {
        [$ok, $res] = saveBase64Image($cropped, 'ads');
        if (!$ok) {
            echo "<script>alert('" . addslashes($res) . "'); window.location.href = '../ads-listing';</script>";
            exit();
        }
        $image_path = $res;
        $new_saved = true;
    }

    $stmt = $conn->prepare("UPDATE ads SET hotel_id = ?, title = ?, image_path = ? WHERE id = ?");
    $stmt->bind_param('issi', $hotel_id, $title, $image_path, $id);
    if ($stmt->execute()) {
        if ($new_saved && $current) {
            $full = dirname(__DIR__) . '/' . $current;
            if (is_file($full)) @unlink($full);
        }
        echo "<script>alert('Ad updated successfully.'); window.location.href = '../ads-listing';</script>";
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "'); window.location.href = '../ads-listing';</script>";
    }
    exit();
}

// Delete ad
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id <= 0) {
        echo "<script>alert('Invalid ID.'); window.location.href = '../ads-listing';</script>";
        exit();
    }

    $current = null;
    $resq = $conn->query("SELECT image_path FROM ads WHERE id = " . $id);
    if ($resq && $resq->num_rows > 0) { $current = $resq->fetch_assoc()['image_path']; }

    $stmt = $conn->prepare("DELETE FROM ads WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        if ($current) {
            $full = dirname(__DIR__) . '/' . $current;
            if (is_file($full)) @unlink($full);
        }
        echo "<script>alert('Ad deleted successfully.'); window.location.href = '../ads-listing';</script>";
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "'); window.location.href = '../ads-listing';</script>";
    }
    exit();
}

// Fallback
header("Location: ../ads-listing");
exit();
