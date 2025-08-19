<?php
session_start();
require_once "../config/config.php";

// Ensure sliders table exists
$conn->query("CREATE TABLE IF NOT EXISTS sliders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

function ensureUploadDir($subdir) {
    $dir = dirname(__DIR__) . "/uploads/" . $subdir . "/";
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        @file_put_contents($dir . 'index.html', '');
    }
    return $dir;
}

function saveBase64Image($base64, $subdir = 'sliders') {
    if (strpos($base64, 'data:image') !== 0) return [false, 'Invalid image data'];
    [$meta, $data] = explode(',', $base64, 2);
    if (!$data) return [false, 'Invalid image data'];
    $ext = 'png';
    if (strpos($meta, 'image/jpeg') !== false) $ext = 'jpg';
    elseif (strpos($meta, 'image/webp') !== false) $ext = 'webp';
    $bin = base64_decode($data);
    if ($bin === false) return [false, 'Failed to decode image'];

    $dir = ensureUploadDir($subdir);
    $name = uniqid('slider_', true) . '.' . $ext;
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

// Add slider
if (isset($_POST['add_slider'])) {
    $title = trim($_POST['title'] ?? '');
    $cropped = $_POST['cropped_image'] ?? '';

    if ($title === '' || $cropped === '') {
        echo "<script>alert('Title and image are required.'); window.location.href = '../slider-listing';</script>";
        exit();
    }

    [$ok, $res] = saveBase64Image($cropped, 'sliders');
    if (!$ok) {
        echo "<script>alert('" . addslashes($res) . "'); window.location.href = '../slider-listing';</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO sliders (title, image_path) VALUES (?, ?)");
    $stmt->bind_param('ss', $title, $res);
    if ($stmt->execute()) {
        echo "<script>alert('Slider added successfully.'); window.location.href = '../slider-listing';</script>";
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "'); window.location.href = '../slider-listing';</script>";
    }
    exit();
}

// Update slider
if (isset($_POST['update_slider'])) {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $cropped = $_POST['cropped_image'] ?? '';

    if ($id <= 0 || $title === '') {
        echo "<script>alert('Invalid input.'); window.location.href = '../slider-listing';</script>";
        exit();
    }

    // Get current image path
    $current = null;
    $resq = $conn->query("SELECT image_path FROM sliders WHERE id = " . $id);
    if ($resq && $resq->num_rows > 0) {
        $current = $resq->fetch_assoc()['image_path'];
    }

    $image_path = $current;
    $new_saved = false;
    if ($cropped !== '') {
        [$ok, $res] = saveBase64Image($cropped, 'sliders');
        if (!$ok) {
            echo "<script>alert('" . addslashes($res) . "'); window.location.href = '../slider-listing';</script>";
            exit();
        }
        $image_path = $res;
        $new_saved = true;
    }

    $stmt = $conn->prepare("UPDATE sliders SET title = ?, image_path = ? WHERE id = ?");
    $stmt->bind_param('ssi', $title, $image_path, $id);
    if ($stmt->execute()) {
        // Delete old file if replaced
        if ($new_saved && $current) {
            $full = dirname(__DIR__) . '/' . $current;
            if (is_file($full)) @unlink($full);
        }
        echo "<script>alert('Slider updated successfully.'); window.location.href = '../slider-listing';</script>";
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "'); window.location.href = '../slider-listing';</script>";
    }
    exit();
}

// Delete slider
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id <= 0) {
        echo "<script>alert('Invalid ID.'); window.location.href = '../slider-listing';</script>";
        exit();
    }

    // Get image path
    $current = null;
    $resq = $conn->query("SELECT image_path FROM sliders WHERE id = " . $id);
    if ($resq && $resq->num_rows > 0) {
        $current = $resq->fetch_assoc()['image_path'];
    }

    $stmt = $conn->prepare("DELETE FROM sliders WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        if ($current) {
            $full = dirname(__DIR__) . '/' . $current;
            if (is_file($full)) @unlink($full);
        }
        echo "<script>alert('Slider deleted successfully.'); window.location.href = '../slider-listing';</script>";
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "'); window.location.href = '../slider-listing';</script>";
    }
    exit();
}

// Fallback
header("Location: ../slider-listing");
exit();
