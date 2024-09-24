<?php
session_start();
include_once("config.php");

$userId = $_SESSION['userId'];
$stmt = $mysqli->prepare("UPDATE tblUsers SET welcome_shown = 1 WHERE userId = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->close();

echo "Welcome message status updated";
?>
