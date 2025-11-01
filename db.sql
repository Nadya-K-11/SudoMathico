CREATE DATABASE sudomathico;
USE sudomathico;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE results (
    result_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    score INT DEFAULT 0,
    attempts INT DEFAULT 0,
    errors INT DEFAULT 0,
    time_taken INT,
    played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE statistics (
    stat_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_games INT DEFAULT 0,
    total_wins INT DEFAULT 0,
    total_score INT DEFAULT 0,
    total_time INT DEFAULT 0,
    total_errors INT DEFAULT 0,
    average_score FLOAT DEFAULT 0,
    average_time FLOAT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE winners (
    winner_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    score INT,
    time_taken INT,
    victory_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    efficiency FLOAT,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE leaderboard (
    leaderboard_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    score INT,
    time_taken INT,
    errors INT,
    attempts INT,
    played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE VIEW leaderboard_view AS
SELECT
    u.username,
    s.total_score,
    s.total_wins,
    s.total_errors,
    s.total_games,
    ROUND(s.total_score / s.total_time, 2) AS efficiency,
    s.average_score,
    s.average_time,
    r.played_at,
    RANK() OVER (ORDER BY s.total_score DESC, efficiency DESC) AS rank
FROM users u
JOIN statistics s ON u.user_id = s.user_id
JOIN (
    SELECT user_id, MAX(played_at) AS played_at
    FROM results
    GROUP BY user_id
)r ON u.user_id = r.user_id;
