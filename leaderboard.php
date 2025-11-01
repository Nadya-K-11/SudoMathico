<?php
include 'db.php';
$query = "
    SELECT username, SUM(score) AS total_score, COUNT(*) AS games_played, 
           SUM(errors) AS total_errors, AVG(time_taken) AS avg_time,
           RANK() OVER (ORDER BY SUM(score) DESC) AS rank
    FROM leaderboard
    GROUP BY username
    ORDER BY total_score DESC;
";
$result = $conn->query($query);

$usernames = [];
$scores = [];
$avgTimes = [];

while ($row = $result->fetch_assoc()) {
    $usernames[] = $row['username'];
    $scores[] = $row['total_score'];
    $avgTimes[] = round($row['avg_time'], 1);
}
?>
<!DOCTYPE html>
<html lang="bg">
<head>
<meta charset="UTF-8">
<title>Leaderboard</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="leaderboard.css">
</head>
<body>

<div class="leaderboard-container">
    <div class="leaderboard-header">Top Players</div>

    <div class="chart-container">
        <canvas id="leaderboardChart"></canvas>
    </div>

    <div class="user-cards">
        <?php
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()):
        ?>
        <div class="user-card">
            <h3><?= htmlspecialchars($row['rank']) ?>. <?= htmlspecialchars($row['username']) ?></h3>
            <p><strong>Score:</strong> <?= $row['total_score'] ?></p>
            <p><strong>Games:</strong> <?= $row['games_played'] ?></p>
            <p><strong>Loses:</strong> <?= $row['total_errors'] ?></p>
            <p><strong>Average Time:</strong> <?= round($row['avg_time'], 1) ?> сек</p>
        </div>
        <?php endwhile; ?>
    </div>
	<div style="text-align:center; margin-top:30px;">
    <a href="sudomathico.php" class="btn-link">Home</a>
    <a href="game.php" class="btn-link">Back to Game</a>
    <a href="info.php" class="btn-link">Info</a>
	<a href="statistics.php" class="btn-link">Stats</a>
    <a href="login.php" class="btn-link">Exit</a>
</div>

<script>
const usernames = <?= json_encode($usernames) ?>;
const scores = <?= json_encode($scores) ?>;
const avgTimes = <?= json_encode($avgTimes) ?>;

const ctx = document.getElementById('leaderboardChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: usernames,
        datasets: [
            {
                label: 'Overall score',
                data: scores,
                backgroundColor: usernames.map(() => `hsl(${Math.random()*360}, 70%, 60%)`),
                borderRadius: 8
            },
            {
                label: 'Average time (sec)',
                data: avgTimes,
                backgroundColor: 'rgba(33, 150, 243, 0.4)',
                borderColor: '#2196F3',
                borderWidth: 2,
                type: 'line',
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: {
                display: true,
                text: 'Results and Players time',
                font: { size: 18 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Score / Time' }
            }
        }
    }
});
</script>
</body>
</html>
