<?php
session_start();
include 'db.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title> Game Info | SudoMathico</title>
<link rel="stylesheet" href="info.css">
</head>
<body>

<div class="info-page">
    <div class="info-box">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <p>
            <strong>SudoMathico</strong> is like classic Sudoku, but not quite…<br><br>
            The board is a standard grid – 9 x 9 (81 cells), but to solve the puzzle you have a certain number of minutes 
            and a certain number of empty cells depending on the difficulty level. 
            You judge your strength yourself! 
        </p>

        <h3>You can choose between 3 levels:</h3>
        <ul>
            <li><strong>Easy:</strong> 20 empty cells, 15 minutes → 25 points</li>
            <li><strong>Medium:</strong> 30 empty cells, 20 minutes → 50 points</li>
            <li><strong>Hard:</strong> 40 empty cells, 30 minutes → 100 points</li>
        </ul>

        <p>
            If you are having a hard time, you can use <strong>Hint</strong> – a mathematical task 
            that reveals the missing digit (costs <span style="color:red;">-10 points</span>).
            With each level, the math tasks become more complicated!<br><br>

            If you run out of time – you can <strong>add 5 minutes</strong> 
            (costs <span style="color:red;">-10 points</span>).<br>
            You can <strong>pause</strong> the game anytime, or <strong>restart</strong> the same puzzle.<br>
            Want a different shuffle? Press <strong>“New Game”</strong>!<br><br>
			
			<strong>Bonus:</strong>You get 100 bonus points for registering and a daily bonus of 20 points for entering the game!<br><br>

            Changing difficulty resets the board for the selected level.<br>
            Exchange your points wisely for time and hints — 
            they are key to reaching the top of the leaderboard!
        </p>

        <h3>Buttons:</h3>
        <ul>
            <li><strong>New Game</strong> – starts a new shuffle of the same difficulty</li>
            <li><strong>Pause</strong> – stops or resumes the timer</li>
            <li><strong>Add 5 minutes</strong> – adds time (−10 points)</li>
            <li><strong>Submit Result</strong> – saves your current score</li>
            <li><strong>Reset</strong> – restarts the current puzzle</li>
            <li><strong>Hint</strong> – shows a math task (−5 points)</li>
            <li><strong>Exit</strong> – leaves the game</li>
            <li><strong>Ranking</strong> – view top 10 players</li>
        </ul>

        <p class="bottom-text">
          <strong>Test your mind, solve the puzzle, collect points… and become the leader!</strong>
        </p>

        <div class="info-buttons">
            <button onclick="window.location.href='game.php'">Start Game</button>
            <button onclick="window.location.href='leaderboard.php'">Leaderboard</button>
            <button onclick="window.location.href='logout.php'">Exit</button>
        </div>
    </div>
</div>

</body>
</html>

