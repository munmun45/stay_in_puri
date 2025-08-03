<?php
require_once '../config/database.php';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid vehicle ID']);
    exit;
}

$id = (int)$_GET['id'];

// Prepare and execute the query
$stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Vehicle not found']);
    exit;
}

// Fetch the vehicle data
$vehicle = $result->fetch_assoc();

// Close connections
$stmt->close();
$conn->close();

// Return the vehicle data as JSON
header('Content-Type: application/json');
echo json_encode($vehicle);
