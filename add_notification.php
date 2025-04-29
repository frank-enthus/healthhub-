<?php
require 'db_connect.php';

$user_id = $_POST['user_id'];
$type = $_POST['type'];
$content = $_POST['content'];

$sql = "INSERT INTO notifications (user_id, type, content) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $type, $content);
$stmt->execute();
$stmt->close();
$conn->close();
?>
