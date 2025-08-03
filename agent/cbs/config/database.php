<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'stay_in_puri';


// $db_host = 'localhost';
// $db_user = 'u112926345_insurances';
// $db_pass = 'zA;zAgZ1V0';
// $db_name = 'u112926345_insurances';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");
?>

