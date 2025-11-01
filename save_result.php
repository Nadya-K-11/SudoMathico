<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['username'])) {
        echo "Error: not logged in";
        exit();
    }

    $username = $_SESSION['username'];
    $score = isset($_POST['score']) ? intval($_POST['score']) : 0;
    $time_taken = isset($_POST['time']) ? intval($_POST['time']) : 0;
    $errors = isset($_POST['error']) ? intval($_POST['error']) : 0;
    $win = isset($_POST['win']) ? intval($_POST['win']) : 0;

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id) {
        echo "User not found";
        exit();
    }

    $attempts = 1;
    $stmt = $conn->prepare("INSERT INTO results (user_id, score, attempts, errors, time_taken, played_at)
                            VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiiii", $user_id, $score, $attempts, $errors, $time_taken);
    $stmt->execute();
    $stmt->close();

    $result = $conn->prepare("SELECT * FROM statistics WHERE user_id = ?");
    $result->bind_param("i", $user_id);
    $result->execute();
    $res = $result->get_result();

    if ($res->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO statistics 
            (user_id, total_games, total_wins, total_score, total_time, total_errors, average_score, average_time)
            VALUES (?, 1, ?, ?, ?, ?, ?, ?)");
        $total_wins = $win ? 1 : 0;
        $avg_score = $score;
        $avg_time = $time_taken;
        $stmt->bind_param("iiiiidd", $user_id, $total_wins, $score, $time_taken, $errors, $avg_score, $avg_time);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("
            UPDATE statistics
            SET 
                total_games = total_games + 1,
                total_wins = total_wins + ?,
                total_score = total_score + ?,
                total_time = total_time + ?,
                total_errors = total_errors + ?,
                average_score = total_score / total_games,
                average_time = total_time / total_games
            WHERE user_id = ?
        ");
        $stmt->bind_param("iiiii", $win, $score, $time_taken, $errors, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    if ($win === 1) {
        $efficiency = $time_taken > 0 ? round($score / $time_taken, 3) : $score;
        $stmt = $conn->prepare("INSERT INTO winners (user_id, score, time_taken, victory_date, efficiency)
                                VALUES (?, ?, ?, NOW(), ?)");
        $stmt->bind_param("iiid", $user_id, $score, $time_taken, $efficiency);
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $conn->prepare("
        INSERT INTO leaderboard (username, score, time_taken, errors, attempts, played_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("siiii", $username, $score, $time_taken, $errors, $attempts);
    $stmt->execute();
    $stmt->close();

    echo "Result saved successfully!";
}
?>
