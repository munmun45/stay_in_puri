<?php
require_once "../config/config.php";
session_start();
// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../login');
    exit();
}

// Handle different actions
if (isset($_POST['action']) || isset($_GET['action'])) {
    $action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];
    
    switch ($action) {
        case 'add':
            addTariff($conn);
            break;
        case 'edit':
            editTariff($conn);
            break;
        case 'delete':
            deleteTariff($conn);
            break;
        default:
            $_SESSION['error_message'] = "Invalid action specified.";
            redirectBack();
            break;
    }
} else {
    $_SESSION['error_message'] = "No action specified.";
    redirectBack();
}

// Function to add a new tariff
function addTariff($conn) {
    // Validate required fields
    $required_fields = ['room_id', 'start_date', 'end_date', 'day_type', 'price'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $_SESSION['error_message'] = "Missing required field: " . $field;
            redirectBack();
            return;
        }
    }
    
    // Get form data
    $room_id = (int)$_POST['room_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $day_type = $_POST['day_type'];
    $price = (float)$_POST['price'];
    $discount_price = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    $cp = !empty($_POST['cp']) ? (float)$_POST['cp'] : null;
    $map = !empty($_POST['map']) ? (float)$_POST['map'] : null;
    $mvp = !empty($_POST['mvp']) ? (float)$_POST['mvp'] : null;
    
    // Validate date range
    if (strtotime($start_date) > strtotime($end_date)) {
        $_SESSION['error_message'] = "Start date cannot be after end date.";
        redirectBack();
        return;
    }
    
    // Validate day type
    $valid_day_types = ['weekday', 'weekend', 'holiday', 'all'];
    if (!in_array($day_type, $valid_day_types)) {
        $_SESSION['error_message'] = "Invalid day type specified.";
        redirectBack();
        return;
    }
    
    // Check if room exists
    $room_check_sql = "SELECT id FROM rooms WHERE id = ?";
    $room_check_stmt = $conn->prepare($room_check_sql);
    $room_check_stmt->bind_param("i", $room_id);
    $room_check_stmt->execute();
    $room_result = $room_check_stmt->get_result();
    
    if ($room_result->num_rows === 0) {
        $_SESSION['error_message'] = "Invalid room selected.";
        redirectBack();
        return;
    }
    
    // Check for overlapping tariffs with the same day type
    $overlap_check_sql = "SELECT id FROM room_tariffs 
                         WHERE room_id = ? AND day_type = ? AND 
                         ((start_date <= ? AND end_date >= ?) OR 
                          (start_date <= ? AND end_date >= ?) OR 
                          (start_date >= ? AND end_date <= ?))";
    
    $overlap_check_stmt = $conn->prepare($overlap_check_sql);
    $overlap_check_stmt->bind_param("isssssss", 
                                  $room_id, $day_type, 
                                  $start_date, $start_date, 
                                  $end_date, $end_date, 
                                  $start_date, $end_date);
    $overlap_check_stmt->execute();
    $overlap_result = $overlap_check_stmt->get_result();
    
    if ($overlap_result->num_rows > 0) {
        $_SESSION['error_message'] = "There is already a tariff for this room with the same day type that overlaps with the selected date range.";
        redirectBack();
        return;
    }
    
    // Insert new tariff
    $insert_sql = "INSERT INTO room_tariffs (room_id, start_date, end_date, day_type, price, discount_price, cp, map, mvp) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("isssddddd", 
                           $room_id, $start_date, $end_date, $day_type, 
                           $price, $discount_price, $cp, $map, $mvp);
    
    if ($insert_stmt->execute()) {
        $_SESSION['success_message'] = "Tariff added successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to add tariff: " . $conn->error;
    }
    
    redirectBack();
}

// Function to edit an existing tariff
function editTariff($conn) {
    // Validate required fields
    $required_fields = ['id', 'room_id', 'start_date', 'end_date', 'day_type', 'price'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $_SESSION['error_message'] = "Missing required field: " . $field;
            redirectBack();
            return;
        }
    }
    
    // Get form data
    $id = (int)$_POST['id'];
    $room_id = (int)$_POST['room_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $day_type = $_POST['day_type'];
    $price = (float)$_POST['price'];
    $discount_price = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    $cp = !empty($_POST['cp']) ? (float)$_POST['cp'] : null;
    $map = !empty($_POST['map']) ? (float)$_POST['map'] : null;
    $mvp = !empty($_POST['mvp']) ? (float)$_POST['mvp'] : null;
    
    // Validate date range
    if (strtotime($start_date) > strtotime($end_date)) {
        $_SESSION['error_message'] = "Start date cannot be after end date.";
        redirectBack();
        return;
    }
    
    // Validate day type
    $valid_day_types = ['weekday', 'weekend', 'holiday', 'all'];
    if (!in_array($day_type, $valid_day_types)) {
        $_SESSION['error_message'] = "Invalid day type specified.";
        redirectBack();
        return;
    }
    
    // Check if tariff exists
    $tariff_check_sql = "SELECT id FROM room_tariffs WHERE id = ? AND room_id = ?";
    $tariff_check_stmt = $conn->prepare($tariff_check_sql);
    $tariff_check_stmt->bind_param("ii", $id, $room_id);
    $tariff_check_stmt->execute();
    $tariff_result = $tariff_check_stmt->get_result();
    
    if ($tariff_result->num_rows === 0) {
        $_SESSION['error_message'] = "Invalid tariff selected.";
        redirectBack();
        return;
    }
    
    // Check for overlapping tariffs with the same day type (excluding the current one)
    $overlap_check_sql = "SELECT id FROM room_tariffs 
                         WHERE room_id = ? AND day_type = ? AND id != ? AND
                         ((start_date <= ? AND end_date >= ?) OR 
                          (start_date <= ? AND end_date >= ?) OR 
                          (start_date >= ? AND end_date <= ?))";
    
    $overlap_check_stmt = $conn->prepare($overlap_check_sql);
    $overlap_check_stmt->bind_param("isissssss", 
                                  $room_id, $day_type, $id,
                                  $start_date, $start_date, 
                                  $end_date, $end_date, 
                                  $start_date, $end_date);
    $overlap_check_stmt->execute();
    $overlap_result = $overlap_check_stmt->get_result();
    
    if ($overlap_result->num_rows > 0) {
        $_SESSION['error_message'] = "There is already another tariff for this room with the same day type that overlaps with the selected date range.";
        redirectBack();
        return;
    }
    
    // Update tariff
    $update_sql = "UPDATE room_tariffs 
                  SET start_date = ?, end_date = ?, day_type = ?, 
                      price = ?, discount_price = ?, cp = ?, map = ?, mvp = ? 
                  WHERE id = ? AND room_id = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssddddiii", 
                           $start_date, $end_date, $day_type, 
                           $price, $discount_price, $cp, $map, $mvp,
                           $id, $room_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['success_message'] = "Tariff updated successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to update tariff: " . $conn->error;
    }
    
    redirectBack();
}

// Function to delete a tariff
function deleteTariff($conn) {
    // Validate required fields
    if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['room_id']) || empty($_GET['room_id'])) {
        $_SESSION['error_message'] = "Missing required parameters for deletion.";
        redirectBack();
        return;
    }
    
    $id = (int)$_GET['id'];
    $room_id = (int)$_GET['room_id'];
    
    // Check if tariff exists
    $tariff_check_sql = "SELECT id FROM room_tariffs WHERE id = ? AND room_id = ?";
    $tariff_check_stmt = $conn->prepare($tariff_check_sql);
    $tariff_check_stmt->bind_param("ii", $id, $room_id);
    $tariff_check_stmt->execute();
    $tariff_result = $tariff_check_stmt->get_result();
    
    if ($tariff_result->num_rows === 0) {
        $_SESSION['error_message'] = "Invalid tariff selected for deletion.";
        redirectBack();
        return;
    }
    
    // Delete tariff
    $delete_sql = "DELETE FROM room_tariffs WHERE id = ? AND room_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $id, $room_id);
    
    if ($delete_stmt->execute()) {
        $_SESSION['success_message'] = "Tariff deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete tariff: " . $conn->error;
    }
    
    redirectBack();
}

// Helper function to redirect back to the previous page
function redirectBack() {
    $room_id = isset($_POST['room_id']) ? $_POST['room_id'] : (isset($_GET['room_id']) ? $_GET['room_id'] : 0);
    header("Location: ../price-maping.php" . ($room_id > 0 ? "?manage_room=" . $room_id : ""));
    exit();
}

/**
 * Get the current tariff for a room based on the date
 * Prioritizes the latest tariff (highest ID) when multiple tariffs exist for the same date
 * 
 * @param mysqli $conn Database connection
 * @param int $room_id Room ID
 * @param string $date Date in Y-m-d format, defaults to current date
 * @return array|null Tariff data or null if no tariff found
 */
function getCurrentTariff($conn, $room_id, $date = null) {
    if ($date === null) {
        $date = date('Y-m-d');
    }
    
    // Query to get current tariffs for the room, ordered by ID DESC to get the latest first
    $sql = "SELECT * FROM room_tariffs 
           WHERE room_id = ? 
           AND start_date <= ? AND end_date >= ? 
           ORDER BY id DESC 
           LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $room_id, $date, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}
?>
