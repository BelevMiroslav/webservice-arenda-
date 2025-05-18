<?php
include 'config.php';
include 'header.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $ad_id = $_GET['id'];
    $query = "SELECT * FROM apartments WHERE id = '$ad_id' AND user_id = '" . $_SESSION['user_id'] . "'";
    $result = $conn->query($query);
    $ad = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    $query = "UPDATE apartments SET 
              address = '$address', 
              district = '$district', 
              rooms = '$rooms', 
              beds = '$beds',
              price = '$price', 
              area = '$area', 
              house_type = '$house_type', 
              floor = '$floor', 
              furniture = '$furniture',
              repair = '$repair', 
              kitchen_area = '$kitchen_area', 
              realtor = '$realtor', 
              description = '$description' 
              WHERE id = '$ad_id'";

    if ($conn->query($query)) {
        echo "<p>Оголошення оновлено!</p>";
    } else {
        echo "<p>Помилка: " . $conn->error . "</p>";
    }
}

?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Редагування оголошення</title>
</head>
<body>
    <div class="form-container">
        <h1>Редагувати оголошення</h1>
        <form method="POST">
            <label>Адреса:</label>
            <input type="text" name="address" value="<?php echo $ad['address']; ?>" required>
            <label>Район:</label>
            <input type="text" name="district" value="<?php echo $ad['district']; ?>" required>
            <label>Кімнати:</label>
            <input type="number" name="rooms" value="<?php echo $ad['rooms']; ?>" required>
            <label>Спальні місця:</label>
            <input type="number" name="beds" value="<?php echo $ad['beds']; ?>" required>
            <label>Ціна:</label>
            <input type="number" step="0.01" name="price" value="<?php echo $ad['price']; ?>" required>
            <label>Площа:</label>
            <input type="number" step="0.01" name="area" value="<?php echo $ad['area']; ?>" required>
            <label>Тип будинку:</label>
            <input type="text" name="house_type" value="<?php echo $ad['house_type']; ?>" required>
            <label>Етаж:</label>
            <input type="number" name="floor" value="<?php echo $ad['floor']; ?>" required>
            <label>Меблі:</label>
            <input type="checkbox" name="furniture" <?php if ($ad['furniture']) echo 'checked'; ?>>
            <label>Ремонт:</label>
            <input type="text" name="repair" value="<?php echo $ad['repair']; ?>" required>
            <label>Площа кухні:</label>
            <input type="number" step="0.01" name="kitchen_area" value="<?php echo $ad['kitchen_area']; ?>" required>
            <label>Рієлтор:</label>
            <input type="checkbox" name="realtor" <?php if ($ad['realtor']) echo 'checked'; ?>>
            <label>Опис:</label>
            <textarea name="description" required><?php echo $ad['description']; ?></textarea>
            <button type="submit">Оновити</button>
        </form>
    </div>
</body>
</html>
