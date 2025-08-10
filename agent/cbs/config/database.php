<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'stay_in_puri';


// $db_host = 'localhost';
// $db_user = 'u112926345_stayinpuri';
// $db_pass = '|R:6AVb4';
// $db_name = 'u112926345_stayinpuri';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");
?>

