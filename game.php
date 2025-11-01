<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT score FROM leaderboard WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($total_score);
$stmt->fetch();
$stmt->close();

$total_score = $total_score ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sudoku Game</title>
    <link rel="stylesheet" href="style.css">
    <script src="sudoku.js" defer></script>
</head>
<body>

<h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

<div id="stats">
    <div id="totalScore">Total Score: 
        <span id="totalScoreValue"><?php echo $total_score; ?></span>
    </div>
    <div id="gameScore">Points (this game): 
        <span id="scoreValue">100</span>
    </div>
    <div id="timer">Time left: 
        <span id="time">--:--</span>
    </div>
</div>

    <table id="sudokuBoard">
        <?php for ($row = 0; $row < 9; $row++): ?>
            <tr>
                <?php for ($col = 0; $col < 9; $col++): ?>
                    <td><input type="text" maxlength="1" class="cell" data-row="<?= $row ?>" data-col="<?= $col ?>"></td>
                <?php endfor; ?>
            </tr>
        <?php endfor; ?>
    </table>

	<div class="difficulty">
        <label for="difficultySelect"><strong>Select Level:</strong></label>
        <select id="difficultySelect" onchange="changeDifficulty()">
            <option value="easy" selected>Easy</option>
            <option value="medium">Medium</option>
            <option value="hard">Hard</option>
        </select>
    </div>

    <div class="buttons">
        <button onclick="newGame()">New Game</button>
        <button onclick="showHint()">Hint</button>
        <button onclick="addTime()">Add 5 Minutes</button>
        <button onclick="resetGame()">Reset</button>
        <button onclick="submitResult()">Submit Result</button>
		<button class="btn-link" onclick="window.location.href='leaderboard.php'">Leaderboard</button>
        <button onclick="exitGame()">Exit</button>
		<button onclick="pauseGame()">Pause</button>
    </div>

    <div id="popup" class="popup"></div>
</body>
</html>

