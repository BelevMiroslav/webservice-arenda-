<?php
include 'config.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $patronymic = $_POST['patronymic'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $phone_main = $_POST['phone_main'];
    $phone_extra1 = $_POST['phone_extra1'];
    $phone_extra2 = $_POST['phone_extra2'];
    $telegram = $_POST['telegram'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $photo = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo = 'uploads/' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }

    $query = "INSERT INTO users (name, surname, patronymic, age, gender, phone_main, phone_extra1, phone_extra2, telegram, photo, email, password) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
              
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Помилка підготовки запиту: " . $conn->error);
    }

    $stmt->bind_param("sssisissssss", $name, $surname, $patronymic, $age, $gender, $phone_main, $phone_extra1, $phone_extra2, $telegram, $photo, $email, $password);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        echo "Помилка виконання запиту: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Реєстрація</title>
</head>
<body>
<div class="form-container">
    <h1>Реєстрація</h1>
    <form action="register.php" method="POST" enctype="multipart/form-data">
        <label for="name">Ім'я:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="surname">Прізвище:</label>
        <input type="text" id="surname" name="surname" required>
        
        <label for="patronymic">По батькові:</label>
        <input type="text" id="patronymic" name="patronymic">
        
        <label for="age">Вік:</label>
        <input type="number" id="age" name="age" required min="0">
        
        <label for="gender">Стать:</label>
        <select id="gender" name="gender" required>
            <option value="male">Чоловік</option>
            <option value="female">Жінка</option>
        </select>
        
        <label for="phone_main">Основний телефон:</label>
        <input type="tel" id="phone_main" name="phone_main" required>
        
        <label for="phone_extra1">Додатковий телефон 1:</label>
        <input type="tel" id="phone_extra1" name="phone_extra1">
        
        <label for="phone_extra2">Додатковий телефон 2:</label>
        <input type="tel" id="phone_extra2" name="phone_extra2">
        
        <label for="telegram">Telegram:</label>
        <input type="text" id="telegram" name="telegram">
        
        <label for="photo">Фото профілю:</label>
        <input type="file" id="photo" name="photo" accept="image/*">
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Зареєструватися</button>
    </form>
</div>
</body>
</html>
