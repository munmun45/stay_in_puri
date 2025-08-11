<?php
require_once "../config/config.php";

// Initialize response
$response = [
    'status' => 'error',
    'message' => '',
    'redirect' => '../offer-listing.php'
];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $offer_id = isset($_POST["offer_id"]) ? trim($_POST["offer_id"]) : "";
    $hotel_id = isset($_POST["hotel_id"]) && !empty($_POST["hotel_id"]) ? trim($_POST["hotel_id"]) : null;
    $room_id = isset($_POST["room_id"]) && !empty($_POST["room_id"]) ? trim($_POST["room_id"]) : null;
    $discount_percentage = isset($_POST["discount_percentage"]) ? trim($_POST["discount_percentage"]) : "";
    $min_amount = isset($_POST["min_amount"]) && !empty($_POST["min_amount"]) ? trim($_POST["min_amount"]) : null;
    $valid_from = isset($_POST["valid_from"]) ? trim($_POST["valid_from"]) : "";
    $valid_to = isset($_POST["valid_to"]) ? trim($_POST["valid_to"]) : "";
    $description = isset($_POST["description"]) ? trim($_POST["description"]) : "";
    $is_active = isset($_POST["is_active"]) ? 1 : 0;

    // Validate form data
    if (empty($discount_percentage) || empty($valid_from) || empty($valid_to)) {
        $response['message'] = "Please fill in all required fields.";
    } elseif (!is_numeric($discount_percentage) || $discount_percentage <= 0 || $discount_percentage > 100) {
        $response['message'] = "Discount percentage must be between 0 and 100.";
    } elseif (!empty($min_amount) && (!is_numeric($min_amount) || $min_amount <= 0)) {
        $response['message'] = "Minimum amount must be a positive number.";
    } elseif (empty($hotel_id) && empty($room_id)) {
        $response['message'] = "Please select either a hotel or a room.";
    } else {
        // Check if offers table exists, if not create it
        $check_table_sql = "SHOW TABLES LIKE 'offers'";
        $table_exists = $conn->query($check_table_sql)->num_rows > 0;
        
        if (!$table_exists) {
            // Create offers table
            $create_table_sql = "CREATE TABLE IF NOT EXISTS `offers` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `hotel_id` int(11) DEFAULT NULL,
                `room_id` int(11) DEFAULT NULL,
                `discount_percentage` decimal(5,2) NOT NULL,
                `min_amount` decimal(10,2) DEFAULT NULL COMMENT 'Minimum amount up to which discount applies',
                `valid_from` date NOT NULL,
                `valid_to` date NOT NULL,
                `description` text DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status: 1=Active, 0=Inactive',
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `hotel_id` (`hotel_id`),
                KEY `room_id` (`room_id`),
                CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
                CONSTRAINT `offers_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            if (!$conn->query($create_table_sql)) {
                $response['message'] = "Error creating offers table: " . $conn->error;
                echo json_encode($response);
                exit;
            }
        }

        // Process the data
        if (empty($offer_id)) {
            // Insert new offer
            if ($hotel_id === null) {
                $hotel_id = "NULL";
            }
            if ($room_id === null) {
                $room_id = "NULL";
            }
            if ($min_amount === null) {
                $min_amount = "NULL";
            }
            
            // Use proper SQL syntax for NULL values
            $sql = "INSERT INTO offers (hotel_id, room_id, discount_percentage, min_amount, valid_from, valid_to, description, is_active) 
                    VALUES (" . 
                    ($hotel_id === "NULL" ? "NULL" : $conn->real_escape_string($hotel_id)) . ", " . 
                    ($room_id === "NULL" ? "NULL" : $conn->real_escape_string($room_id)) . ", " . 
                    $conn->real_escape_string($discount_percentage) . ", " . 
                    ($min_amount === "NULL" ? "NULL" : $conn->real_escape_string($min_amount)) . ", " . 
                    "'" . $conn->real_escape_string($valid_from) . "', " . 
                    "'" . $conn->real_escape_string($valid_to) . "', " . 
                    "'" . $conn->real_escape_string($description) . "', " . 
                    $is_active . ")";
            
            if ($conn->query($sql)) {
                $response['status'] = 'success';
                $response['message'] = "Offer added successfully!";
            } else {
                $response['message'] = "Error adding offer: " . $conn->error;
            }
        } else {
            // Update existing offer
            if ($hotel_id === null) {
                $hotel_id = "NULL";
            }
            if ($room_id === null) {
                $room_id = "NULL";
            }
            if ($min_amount === null) {
                $min_amount = "NULL";
            }
            
            // Use proper SQL syntax for NULL values
            $sql = "UPDATE offers SET 
                    hotel_id = " . ($hotel_id === "NULL" ? "NULL" : $conn->real_escape_string($hotel_id)) . ", 
                    room_id = " . ($room_id === "NULL" ? "NULL" : $conn->real_escape_string($room_id)) . ", 
                    discount_percentage = " . $conn->real_escape_string($discount_percentage) . ", 
                    min_amount = " . ($min_amount === "NULL" ? "NULL" : $conn->real_escape_string($min_amount)) . ", 
                    valid_from = '" . $conn->real_escape_string($valid_from) . "', 
                    valid_to = '" . $conn->real_escape_string($valid_to) . "', 
                    description = '" . $conn->real_escape_string($description) . "', 
                    is_active = " . $is_active . " 
                    WHERE id = " . $conn->real_escape_string($offer_id);
            
            if ($conn->query($sql)) {
                $response['status'] = 'success';
                $response['message'] = "Offer updated successfully!";
            } else {
                $response['message'] = "Error updating offer: " . $conn->error;
            }
        }
    }
} 
// Process toggle status request
else if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["toggle_status"]) && isset($_GET["current"])) {
    $id = (int)$_GET["toggle_status"];
    $current_status = (int)$_GET["current"];
    $new_status = $current_status ? 0 : 1; // Toggle the status
    
    $update_sql = "UPDATE offers SET is_active = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $new_status, $id);
    
    if ($stmt->execute()) {
        $_SESSION["success_msg"] = "Offer status updated successfully.";
    } else {
        $_SESSION["error_msg"] = "Error updating offer status: " . $conn->error;
    }
    
    header("Location: ../offer-listing.php");
    exit();
}
// Process delete request
else if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["delete"]) && !empty($_GET["delete"])) {
    $delete_id = $_GET["delete"];
    $sql = "DELETE FROM offers WHERE id = " . $conn->real_escape_string($delete_id);
    
    if ($conn->query($sql)) {
        $response['status'] = 'success';
        $response['message'] = "Offer deleted successfully!";
    } else {
        $response['message'] = "Error deleting offer: " . $conn->error;
    }
} else {
    $response['message'] = "Invalid request method";
}

// Store message in session
session_start();
$_SESSION['offer_message'] = $response['message'];
$_SESSION['offer_status'] = $response['status'];

// Redirect back to the listing page
header("Location: " . $response['redirect']);
exit;
?>
