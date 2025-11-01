<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT total_score FROM statistics WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($total_score);
$stmt->fetch();
$stmt->close();

if (!$total_score) $total_score = 100;

echo json_encode(['total_score' => $total_score]);
?>
