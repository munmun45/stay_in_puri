<?php
session_start();
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Vehicle deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting vehicle: " . $conn->error;
    }

    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid request.";
}

header("Location: ../vehicles.php");
exit;
?>
