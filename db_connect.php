<?php
$servername = "localhost";
$username = "root";
$password = "";  // Keep empty for XAMPP
$database = "Healthhub";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
