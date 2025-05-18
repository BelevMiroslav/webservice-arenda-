<?php
include 'config.php';
include 'header.php';

$query = "SELECT * FROM apartments WHERE status = 'approved'";

if (isset($_GET['search'])) {
    $filters = [];
    if (!empty($_GET['district'])) {
        $district = $conn->real_escape_string($_GET['district']);
        $filters[] = "district = '$district'";
    }
    if (!empty($_GET['rooms'])) {
        $rooms = (int)$_GET['rooms'];
        $filters[] = "rooms = $rooms";
    }
    if (!empty($_GET['beds'])) {
        $beds = (int)$_GET['beds'];
        $filters[] = "beds >= $beds";
    }
    if (!empty($_GET['price_min'])) {
        $price_min = (float)$_GET['price_min'];
        $filters[] = "price >= $price_min";
    }
    if (!empty($_GET['price_max'])) {
        $price_max = (float)$_GET['price_max'];
        $filters[] = "price <= $price_max";
    }
    if (!empty($_GET['area_min'])) {
        $area_min = (float)$_GET['area_min'];
        $filters[] = "area >= $area_min";
    }
    if (!empty($_GET['area_max'])) {
        $area_max = (float)$_GET['area_max'];
        $filters[] = "area <= $area_max";
    }
    if (!empty($_GET['house_type'])) {
        $house_type = $conn->real_escape_string($_GET['house_type']);
        $filters[] = "house_type = '$house_type'";
    }
    if (!empty($_GET['floor'])) {
        $floor = (int)$_GET['floor'];
        $filters[] = "floor = $floor";
    }
    if (!empty($_GET['furniture'])) {
        $furniture = (int)$_GET['furniture'];
        $filters[] = "furniture = $furniture";
    }
    if (!empty($_GET['repair'])) {
        $repair = $conn->real_escape_string($_GET['repair']);
        $filters[] = "repair = '$repair'";
    }
    if (!empty($_GET['kitchen_area_min'])) {
        $kitchen_area_min = (float)$_GET['kitchen_area_min'];
        $filters[] = "kitchen_area >= $kitchen_area_min";
    }
    if (!empty($_GET['kitchen_area_max'])) {
        $kitchen_area_max = (float)$_GET['kitchen_area_max'];
        $filters[] = "kitchen_area <= $kitchen_area_max";
    }
    if (!empty($_GET['realtor'])) {
        $realtor = (int)$_GET['realtor'];
        $filters[] = "realtor = $realtor";
    }
    if ($filters) {
        $query .= " AND " . implode(' AND ', $filters);
    }
}

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <link rel="stylesheet" href="css/style.css">
    <link href='https://fonts.googleapis.com/css?family=Azeret Mono' rel='stylesheet'>
    <title>Оренда квартир</title>
</head>
<body>
    <header2>
    <div class="header-container">
            <div class="logo">
                <img src="images/logo.png" alt="Логотип" />
            </div>
            <div class="slogan">
                <p>Знайдіть свій затишок на довгий термін!</p>
            </div>
        </div>
        <form method="GET" class="filters">
            <select name="district">
                <option value="">Район</option>
                <?php
                $districts = ['Олександрівський', 'Заводський', 'Комунарський', 'Дніпровський', 'Вознесенівський', 'Хортицький', 'Шевченківський'];
                foreach ($districts as $district) {
                    $selected = isset($_GET['district']) && $_GET['district'] === $district ? 'selected' : '';
                    echo "<option value='$district' $selected>$district</option>";
                }
                ?>
            </select>
            <select name="rooms">
                <option value="">Кількість кімнат</option>
                <option value="1" <?php echo isset($_GET['rooms']) && $_GET['rooms'] == '1' ? 'selected' : ''; ?>>1</option>
                <option value="2" <?php echo isset($_GET['rooms']) && $_GET['rooms'] == '2' ? 'selected' : ''; ?>>2</option>
                <option value="3" <?php echo isset($_GET['rooms']) && $_GET['rooms'] == '3' ? 'selected' : ''; ?>>3+</option>
            </select>
            <input type="number" name="beds" placeholder="Спальні місця" value="<?php echo $_GET['beds'] ?? ''; ?>">
            <input type="number" step="0.01" name="price_min" placeholder="Мін. ціна" value="<?php echo $_GET['price_min'] ?? ''; ?>">
            <input type="number" step="0.01" name="price_max" placeholder="Макс. ціна" value="<?php echo $_GET['price_max'] ?? ''; ?>">
            <input type="number" step="0.01" name="area_min" placeholder="Мін. площа" value="<?php echo $_GET['area_min'] ?? ''; ?>">
            <input type="number" step="0.01" name="area_max" placeholder="Макс. площа" value="<?php echo $_GET['area_max'] ?? ''; ?>">
            <select name="house_type">
                <option value="">Тип будинку</option>
                <?php
                $house_types = ['Царський будинок', 'Сталінка', 'Хрущовка', 'Чешка', 'Гостинка', 'Совмін', 'Гуртожиток', 'Житловий фонд 80-90-і', 'Житловий фонд 91-2000-і', 'Житловий фонд 2001-2010-і', 'Житловий фонд від 2011 р.'];
                foreach ($house_types as $type) {
                    $selected = isset($_GET['house_type']) && $_GET['house_type'] === $type ? 'selected' : '';
                    echo "<option value='$type' $selected>$type</option>";
                }
                ?>
            </select>
            <input type="number" name="floor" placeholder="Поверх" value="<?php echo $_GET['floor'] ?? ''; ?>">
            <select name="furniture">
                <option value="">Меблі</option>
                <option value="1" <?php echo isset($_GET['furniture']) && $_GET['furniture'] == '1' ? 'selected' : ''; ?>>Так</option>
                <option value="0" <?php echo isset($_GET['furniture']) && $_GET['furniture'] == '0' ? 'selected' : ''; ?>>Ні</option>
            </select>
            <select name="repair">
                <option value="">Ремонт</option>
                <?php
                $repairs = ['Авторський проект', 'Євроремонт', 'Косметичний ремонт', 'Житловий стан', 'Після будівельників', 'Під чистову обробку', 'Аварійний стан'];
                foreach ($repairs as $repair) {
                    $selected = isset($_GET['repair']) && $_GET['repair'] === $repair ? 'selected' : '';
                    echo "<option value='$repair' $selected>$repair</option>";
                }
                ?>
            </select>
            <input type="number" step="0.01" name="kitchen_area_min" placeholder="Мін. площа кухні" value="<?php echo $_GET['kitchen_area_min'] ?? ''; ?>">
            <input type="number" step="0.01" name="kitchen_area_max" placeholder="Макс. площа кухні" value="<?php echo $_GET['kitchen_area_max'] ?? ''; ?>">
            <select name="realtor">
                <option value="">Рієлтор</option>
                <option value="1" <?php echo isset($_GET['realtor']) && $_GET['realtor'] == '1' ? 'selected' : ''; ?>>Так</option>
                <option value="0" <?php echo isset($_GET['realtor']) && $_GET['realtor'] == '0' ? 'selected' : ''; ?>>Ні</option>
            </select>
            <button type="submit" name="search">Пошук</button>
        </form>
    </header2>
    <main>
        <div class="listings">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="listing" onclick="window.location.href='ad.php?id=<?php echo $row['id']; ?>'">
                        <img src="uploads/<?php echo !empty($row['photo1']) ? $row['photo1'] : 'default-image.jpg'; ?>" alt="Фото">
                        <p class="price"><?php echo number_format($row['price'], 2) . ' грн'; ?></p>
                        <div class="details">
                            <p><?php echo $row['address']; ?></p>
                            <p>Кімнат: <?php echo $row['rooms']; ?> | Площа: <?php echo $row['area']; ?> м²</p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Оголошень не знайдено.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
