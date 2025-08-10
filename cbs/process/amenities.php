<?php
session_start();
require("../config/config.php");

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login");
    exit();
}

// Add new amenity
if (isset($_POST['add_amenity'])) {
    $title = trim($_POST['title']);
    $icon = trim($_POST['icon']);
    
    if (empty($title) || empty($icon)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href = '../amenities';</script>";
        exit();
    }
    
    $sql = "INSERT INTO amenities (title, icon) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $title, $icon);
    
    if ($stmt->execute()) {
        echo "<script>alert('Amenity added successfully.'); window.location.href = '../amenities';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = '../amenities';</script>";
    }
    exit();
}

// Update existing amenity
if (isset($_POST['update_amenity'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $icon = trim($_POST['icon']);
    
    if (empty($title) || empty($icon)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href = '../amenities';</script>";
        exit();
    }
    
    $sql = "UPDATE amenities SET title = ?, icon = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $icon, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Amenity updated successfully.'); window.location.href = '../amenities';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = '../amenities';</script>";
    }
    exit();
}

// Delete amenity
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $sql = "DELETE FROM amenities WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Amenity deleted successfully.'); window.location.href = '../amenities';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = '../amenities';</script>";
    }
    exit();
}
?>
