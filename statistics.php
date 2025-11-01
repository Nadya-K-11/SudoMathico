<?php
include 'db.php';

$query = "
    SELECT 
        COUNT(user_id) AS total_players,
        SUM(total_games) AS total_games,
		SUM(total_time) AS total_time,
        SUM(total_wins) AS total_wins,
        SUM(total_score) AS total_score,
        ROUND(AVG(average_score),1) AS avg_score,
        ROUND(AVG(average_time),1) AS avg_time,
        SUM(total_errors) AS total_errors
    FROM statistics
";
$stats = $conn->query($query)->fetch_assoc();

$playersQuery = "
    SELECT u.username, s.total_games, s.total_wins, s.total_score, 
           s.average_score, s.average_time, s.total_errors
    FROM statistics s
    JOIN users u ON s.user_id = u.user_id
    ORDER BY s.total_score DESC
";
$players = $conn->query($playersQuery);
?>
<!DOCTYPE html>
<html lang="bg">
<head>
<meta charset="UTF-8">
<title>Game Statistics</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="statistics.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="statistics-container">
    <h1>Game Statistics</h1>

    <div class="summary-grid">
        <div><strong>Players:</strong> <?= $stats['total_players'] ?></div>
        <div><strong>Total Games:</strong> <?= $stats['total_games'] ?></div>
		<div><strong>Total Time:</strong> <?= $stats['total_time'] ?></div>
		<div><strong>Total Score:</strong> <?= $stats['total_score'] ?></div>        
		<div><strong>Total Wins:</strong> <?= $stats['total_wins'] ?></div>
		<div><strong>Average Score:</strong> <?= $stats['avg_score'] ?></div>
		<div><strong>Average Time:</strong> <?= $stats['avg_time'] ?></div>
