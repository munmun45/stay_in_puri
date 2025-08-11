<?php
/**
 * Utility functions for tariff management
 */

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
