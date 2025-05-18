<?php
include 'config.php';
include 'header.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php');
        } else {
            echo "<p>Невірний пароль!</p>";
        }
    } else {
        echo "<p>Користувач із таким email не знайдений!</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Авторизація</title>
</head>
<body>
    <div class="form-container">
        <h1>Авторизація</h1>
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Пароль:</label>
            <input type="password" name="password" required>
            <button type="submit">Увійти</button>
        </form>
    </div>
</body>
</html>
