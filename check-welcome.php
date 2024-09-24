<?php
session_start();
include_once("config.php");

$userId = $_SESSION['userId'];
$stmt = $mysqli->prepare("SELECT welcome_shown FROM tblUsers WHERE userId = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($welcome_shown);
$stmt->fetch();
$stmt->close();

echo json_encode(['showMessage' => !$welcome_shown]);
?>
