<?php
include 'config.php';
include 'header.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $rooms = $_POST['rooms'];
    $beds = $_POST['beds'];
    $price = $_POST['price'];
    $area = $_POST['area'];
    $house_type = $_POST['house_type'];
    $floor = $_POST['floor'];
    $furniture = isset($_POST['furniture']) ? 1 : 0;
    $repair = $_POST['repair'];
    $kitchen_area = $_POST['kitchen_area'];
    $realtor = isset($_POST['realtor']) ? 1 : 0;
    $description = $_POST['description'];

    $photo1 = $_FILES['photo1']['name'];
    $photo2 = $_FILES['photo2']['name'];
    $photo3 = $_FILES['photo3']['name'];
    move_uploaded_file($_FILES['photo1']['tmp_name'], "uploads/$photo1");
    move_uploaded_file($_FILES['photo2']['tmp_name'], "uploads/$photo2");
    move_uploaded_file($_FILES['photo3']['tmp_name'], "uploads/$photo3");

    $query = "INSERT INTO apartments (user_id, address, district, rooms, beds, price, area, house_type, floor, 
        furniture, repair, kitchen_area, realtor, description, photo1, photo2, photo3) 
        VALUES ('$user_id', '$address', '$district', '$rooms', '$beds', '$price', '$area', '$house_type', '$floor', 
        '$furniture', '$repair', '$kitchen_area', '$realtor', '$description', '$photo1', '$photo2', '$photo3')";

    if ($conn->query($query)) {
        echo "
        <div id='modal' class='modal'>
            <div class='modal-content'>
                <h2>Оголошення додано</h2>
                <p>Оголошення додано і знаходиться на розгляді адміністратора.</p>
                <button id='closeModal' class='btn-close'>ОК</button>
            </div>
        </div>
        <script>
            document.getElementById('closeModal').onclick = function() {
                document.getElementById('modal').style.display = 'none';
            }
            document.getElementById('modal').style.display = 'block';
        </script>
        ";
    } else {
        echo "<p>Помилка: " . $conn->error . "</p>";
    }
}



?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Додавання оголошення</title>
</head>
<body>
    <div class="form-container">
        <h1>Додати оголошення</h1>
        <form method="POST" enctype="multipart/form-data">
            <label>Адреса:</label>
            <input type="text" name="address" required>

            <label>Район:</label>
            <select name="district" required>
                <option value="Вознесенівський">Вознесенівський</option>
                <option value="Дніпровський">Дніпровський</option>
                <option value="Заводський">Заводський</option>
                <option value="Комунарський">Комунарський</option>
                <option value="Олександрівський">Олександрівський</option>
                <option value="Хортицький">Хортицький</option>
                <option value="Шевченківський">Шевченківський</option>
            </select>

            <label>Кімнати:</label>
            <input type="number" name="rooms" required>

            <label>Спальні місця:</label>
            <input type="number" name="beds" required>

            <label>Ціна:</label>
            <input type="number" step="1" name="price" required>

            <label>Площа:</label>
            <input type="number" step="1" name="area" required>

            <label>Тип будинку:</label>
            <select name="house_type" required>
                <option value="Царський будинок">Царський будинок</option>
                <option value="Сталінка">Сталінка</option>
                <option value="Хрущовка">Хрущовка</option>
                <option value="Чешка">Чешка</option>
                <option value="Гостинка">Гостинка</option>
                <option value="Совмін">Совмін</option>
                <option value="Гуртожиток">Гуртожиток</option>
                <option value="Житловий фонд 80-90-і">Житловий фонд 80-90-і</option>
                <option value="Житловий фонд 91-2000-і">Житловий фонд 91-2000-і</option>
                <option value="Житловий фонд 2001-2010-і">Житловий фонд 2001-2010-і</option>
                <option value="Житловий фонд від 2011 р.">Житловий фонд від 2011 р.</option>
                <option value="Авторський проект">Авторський проект</option>
            </select>

            <label>Поверх:</label>
            <input type="number" name="floor" required>

            <label>Меблі:</label>
            <input type="checkbox" name="furniture">

            <label>Ремонт:</label>
            <select name="repair" required>
                <option value="Авторський проект">Авторський проект</option>
                <option value="Євроремонт">Євроремонт</option>
                <option value="Косметичний ремонт">Косметичний ремонт</option>
                <option value="Житловий стан">Житловий стан</option>
                <option value="Після будівельників">Після будівельників</option>
                <option value="Під чистову обробку">Під чистову обробку</option>
                <option value="Аварійний стан">Аварійний стан</option>
            </select>

            <label>Площа кухні:</label>
            <input type="number" step="1" name="kitchen_area" required>

            <label>Рієлтор:</label>
            <input type="checkbox" name="realtor">

            <label>Опис:</label>
            <textarea name="description" required></textarea>

            <label>Фото 1:</label>
            <input type="file" name="photo1" required>

            <label>Фото 2:</label>
            <input type="file" name="photo2" required>

            <label>Фото 3:</label>
            <input type="file" name="photo3" required>

            <button type="submit">Додати</button>
        </form>
    </div>
</body>
</html>
