<?php
session_start();
include 'db.php';
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>SudoMathico</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-bg">

<div class="auth-form">
    <h2><strong>Welcome in SudoMathico!</strong></h2>
    <p style="font-size: 16px; line-height: 1.6;">
      Solve the Sudoku!<br>
		Climb the leaderboard!<br>
        If you are having difficulty with a position — help yourself with the task according to your knowledge of mathematics!<br><br>
        <strong>Test your knowledge, your mind and your speed!</strong>
    </p>

    <img src="img/sudomathico_logo.png" alt="SudoMathico" style="width:180px; margin:15px auto; display:block; border-radius:15px; box-shadow:0 2px 10px rgba(0,0,0,0.2);">

    <button class="btn-primary" onclick="window.location.href='login.php'">Enter</button>
    <button class="btn-primary" onclick="window.location.href='info.php'">Info</button>
    <button class="btn-primary" onclick="window.location.href='game.php'">Start Game</button>
    <button class="btn-primary" onclick="window.location.href='leaderboard.php'">Leaderboard</button>
    <button class="btn-primary" onclick="window.location.href='login.php'">Exit</button>
</div>

</body>
</html>
