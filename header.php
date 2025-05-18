<?php
session_start();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Оренда квартир</title>
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="images/logo.png" alt="Логотип">
        </div>
        <nav>
            <a href="index.php" class="btn">Головна</a>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <a href="user_profile.php" class="btn">Мій профіль</a>
                <?php if ($_SESSION['role'] == 'admin') { ?>
                    <a href="admin.php" class="btn">Адмінпанель</a>
                <?php } ?>
                <a href="logout.php" class="btn">Вийти</a>
            <?php } else { ?>
                <a href="login.php" class="btn">Увійти</a>
                <a href="register.php" class="btn">Реєстрація</a>
            <?php } ?>
        </nav>
    </header>
</body>
</html>
