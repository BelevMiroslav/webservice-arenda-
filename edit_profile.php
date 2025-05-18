<?php
include 'config.php';
include 'header.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = $conn->query($query);
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $patronymic = $_POST['patronymic'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $phone_main = $_POST['phone_main'];
    $phone_extra1 = $_POST['phone_extra1'];
    $phone_extra2 = $_POST['phone_extra2'];
    $telegram = $_POST['telegram'];

    $photo = $user['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "uploads/";
        $original_file = $_FILES["photo"]["tmp_name"];
        $imageFileType = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $max_width = 800;
        $max_height = 800;
    
        if (in_array($imageFileType, $allowed_types)) {
            list($width, $height) = getimagesize($original_file);
            
            switch ($imageFileType) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($original_file);
                    break;
                case 'png':
                    $image = imagecreatefrompng($original_file);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($original_file);
                    break;
                default:
                    echo "Недопустимий формат файлу.";
                    exit();
            }
    
            if ($width > $max_width || $height > $max_height) {
                $ratio = min($max_width / $width, $max_height / $height);
                $new_width = (int)($width * $ratio);
                $new_height = (int)($height * $ratio);
                $resized_image = imagecreatetruecolor($new_width, $new_height);
    
                if ($imageFileType == 'png' || $imageFileType == 'gif') {
                    imagealphablending($resized_image, false);
                    imagesavealpha($resized_image, true);
                }
    
                imagecopyresampled($resized_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
                $image = $resized_image;
            }
    
            $new_filename = $target_dir . uniqid() . ".$imageFileType";
            switch ($imageFileType) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($image, $new_filename, 90);
                    break;
                case 'png':
                    imagepng($image, $new_filename);
                    break;
                case 'gif':
                    imagegif($image, $new_filename);
                    break;
            }
    
            imagedestroy($image);
    
            $photo = $new_filename;
        } else {
            echo "Недопустимий формат файлу.";
        }
    }
    

    $update_query = "UPDATE users SET 
        surname = ?, 
        name = ?, 
        patronymic = ?, 
        age = ?, 
        gender = ?, 
        phone_main = ?, 
        phone_extra1 = ?, 
        phone_extra2 = ?, 
        telegram = ?, 
        photo = ? 
        WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssisissssi", $surname, $name, $patronymic, $age, $gender, $phone_main, $phone_extra1, $phone_extra2, $telegram, $photo, $user_id);
    if ($stmt->execute()) {
        header('Location: user_profile.php');
        exit();
    } else {
        echo "Помилка оновлення даних.";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Редагування профілю</title>
</head>
<body>
    <div class="form-container">
        <h1>Редагування профілю</h1>
        <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
            <label for="surname">Прізвище:</label>
            <input type="text" id="surname" name="surname" value="<?php echo $user['surname']; ?>" required>

            <label for="name">Ім'я:</label>
            <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>

            <label for="patronymic">По батькові:</label>
            <input type="text" id="patronymic" name="patronymic" value="<?php echo $user['patronymic']; ?>">

            <label for="age">Вік:</label>
            <input type="number" id="age" name="age" value="<?php echo $user['age']; ?>" required>

            <label for="gender">Стать:</label>
            <select id="gender" name="gender">
                <option value="male" <?php echo $user['gender'] == 'male' ? 'selected' : ''; ?>>Чоловік</option>
                <option value="female" <?php echo $user['gender'] == 'female' ? 'selected' : ''; ?>>Жінка</option>
            </select>

            <label for="phone_main">Основний телефон:</label>
            <input type="text" id="phone_main" name="phone_main" value="<?php echo $user['phone_main']; ?>" required>

            <label for="phone_extra1">Додатковий телефон 1:</label>
            <input type="text" id="phone_extra1" name="phone_extra1" value="<?php echo $user['phone_extra1']; ?>">

            <label for="phone_extra2">Додатковий телефон 2:</label>
            <input type="text" id="phone_extra2" name="phone_extra2" value="<?php echo $user['phone_extra2']; ?>">

            <label for="telegram">Telegram:</label>
            <input type="text" id="telegram" name="telegram" value="<?php echo $user['telegram']; ?>">

            <label for="photo">Фото профілю:</label>
            <input type="file" id="photo" name="photo" accept="image/*">

            <button type="submit">Зберегти зміни</button>
        </form>
    </div>
</body>
</html>
