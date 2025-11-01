<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $input, $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            $_SESSION["username"] = $user["username"];
            $_SESSION["user_id"] = $user["user_id"];

            $stmt2 = $conn->prepare("INSERT INTO results (user_id, score, time_taken) VALUES (?, 20, 0)");
            $stmt2->bind_param("i", $user["user_id"]);
            $stmt2->execute();

            echo "<script>
                alert('Welcome, " . $user["username"] . "!\\nYou earned 20 points!');
                window.location.href='info.php'
            </script>";
        } else {
            echo "<script>alert('Incorrect password.'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('User not found.'); window.location.href='index.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
