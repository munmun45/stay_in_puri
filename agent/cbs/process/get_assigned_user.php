<?php
require_once '../config/database.php';

// Set headers for JSON response
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Check if vehicle_id is provided and valid
if (!isset($_GET['vehicle_id']) || !is_numeric($_GET['vehicle_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid vehicle ID']);
    exit;
}

$vehicleId = (int)$_GET['vehicle_id'];
$response = ['user_id' => null];

try {
    // Get the assigned user ID for this vehicle
    $stmt = $conn->prepare("SELECT user_id FROM vehicle_assignments WHERE vehicle_id = ? AND status = 'active' ORDER BY assigned_date DESC LIMIT 1");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $vehicleId);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $response['user_id'] = (int)$row['user_id'];
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(500);
    $response = ['error' => 'Database error: ' . $e->getMessage()];
}

// Ensure we always return valid JSON
echo json_encode($response, JSON_NUMERIC_CHECK);

if (isset($conn) && $conn) {
    $conn->close();
}
?>
