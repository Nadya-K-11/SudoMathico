<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.location.href='register.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['username'] = $username;
        echo "<script>alert('Welcome! Successful Registration!'); window.location.href='sudomathico.php';</script>";
    } else {
        echo "<script>alert('Registration failed. Try again.'); window.location.href='register.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
