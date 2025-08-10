<?php
/**
 * Prices Processing File
 * Handles price operations for rooms including:
 * - Saving room prices (create or update)
 */

// Start the session first (if not already started)
session_start();

// Include database configuration
require('../config/config.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    $response = [
        'success' => false,
        'message' => 'Authentication required'
    ];
    echo json_encode($response);
    exit();
}

// Handle save room prices request
if (isset($_POST['save_room_prices'])) {
    // Collect data from POST
    $room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : 0;
    $price_id = isset($_POST['price_id']) ? (int)$_POST['price_id'] : 0;
    $main_price = isset($_POST['main_price']) ? (float)$_POST['main_price'] : 0;
    $discount_price = isset($_POST['discount_price']) ? (float)$_POST['discount_price'] : 0;
    $cp = isset($_POST['cp']) ? (float)$_POST['cp'] : 0;
    $map = isset($_POST['map']) ? (float)$_POST['map'] : 0;
    $mvp = isset($_POST['mvp']) ? (float)$_POST['mvp'] : 0;
    
    // Validate input
    if ($room_id <= 0) {
        $response = [
            'success' => false,
            'message' => 'Invalid room ID'
        ];
        echo json_encode($response);
        exit();
    }
    
    // Verify room exists
    $check_room = "SELECT id FROM rooms WHERE id = ?";
    $check_stmt = $conn->prepare($check_room);
    $check_stmt->bind_param('i', $room_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        $response = [
            'success' => false,
            'message' => 'Room not found'
        ];
        echo json_encode($response);
        exit();
    }
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        if ($price_id > 0) {
            // Update existing price record
            $update_sql = "UPDATE room_prices SET 
                           main_price = ?, 
                           discount_price = ?, 
                           cp = ?, 
                           map = ?, 
                           mvp = ?
                           WHERE id = ? AND room_id = ?";
            
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param('dddddii', 
                $main_price, 
                $discount_price, 
                $cp, 
                $map, 
                $mvp, 
                $price_id, 
                $room_id
            );
            
            if (!$update_stmt->execute()) {
                throw new Exception("Failed to update price: " . $conn->error);
            }
            
            // Check if record was actually updated
            if ($update_stmt->affected_rows === 0) {
                // The price_id might not exist or doesn't belong to this room
                // Try to check if the price record exists
                $verify_sql = "SELECT id FROM room_prices WHERE id = ? AND room_id = ?";
                $verify_stmt = $conn->prepare($verify_sql);
                $verify_stmt->bind_param('ii', $price_id, $room_id);
                $verify_stmt->execute();
                
                if ($verify_stmt->get_result()->num_rows === 0) {
                    // Price record doesn't exist for this room, insert a new one
                    $insert_sql = "INSERT INTO room_prices (room_id, main_price, discount_price, cp, map, mvp) 
                                  VALUES (?, ?, ?, ?, ?, ?)";
                    
                    $insert_stmt = $conn->prepare($insert_sql);
                    $insert_stmt->bind_param('iddddd', 
                        $room_id, 
                        $main_price, 
                        $discount_price, 
                        $cp, 
                        $map, 
                        $mvp
                    );
                    
                    if (!$insert_stmt->execute()) {
                        throw new Exception("Failed to insert price: " . $conn->error);
                    }
                    
                    $price_id = $conn->insert_id;
                } else {
                    throw new Exception("Price record verification failed");
                }
            }
        } else {
            // Insert new price record
            $insert_sql = "INSERT INTO room_prices (room_id, main_price, discount_price, cp, map, mvp) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param('iddddd', 
                $room_id, 
                $main_price, 
                $discount_price, 
                $cp, 
                $map, 
                $mvp
            );
            
            if (!$insert_stmt->execute()) {
                throw new Exception("Failed to insert price: " . $conn->error);
            }
            
            $price_id = $conn->insert_id;
        }
        
        // Commit transaction
        $conn->commit();
        
        $response = [
            'success' => true,
            'message' => 'Price saved successfully',
            'price_id' => $price_id
        ];
        echo json_encode($response);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        $response = [
            'success' => false,
            'message' => $e->getMessage()
        ];
        echo json_encode($response);
    }
    
    exit();
}

// If we get here, no valid action was requested
$response = [
    'success' => false,
    'message' => 'Invalid request'
];
echo json_encode($response);
