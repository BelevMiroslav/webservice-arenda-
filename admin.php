<?php
include 'config.php';
include 'header.php';

session_start();

if ($_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

if (isset($_GET['approve'])) {
    $ad_id = $_GET['approve'];
    $query = "UPDATE apartments SET status = 'approved' WHERE id = '$ad_id'";
    $conn->query($query);
}

if (isset($_GET['reject'])) {
    $ad_id = $_GET['reject'];
    $query = "UPDATE apartments SET status = 'rejected', rejected_at = NOW() WHERE id = '$ad_id'";
    $conn->query($query);
}

if (isset($_GET['delete'])) {
    $ad_id = $_GET['delete'];
    $query = "DELETE FROM apartments WHERE id = '$ad_id'";
    $conn->query($query);
}

$query = "SELECT * FROM apartments ORDER BY status DESC, id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Адмінпанель</title>
</head>
<body>
    <a href="report_all.php" class="btn-report">Загальний звіт</a>
    <table>
        <tr>
            <th>Адреса</th>
            <th>Ціна</th>
            <th>Тип будинку</th>
            <th>Статус</th>
            <th>Дії</th>
            <th>Деталі</th>
        </tr>
        <?php while ($ad = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $ad['address']; ?></td>
            <td><?php echo $ad['price']; ?> грн</td>
            <td><?php echo $ad['house_type']; ?></td>
            <td>
                <?php 
                    if ($ad['status'] == 'approved') echo 'Підтверджено';
                    elseif ($ad['status'] == 'rejected') echo 'Відхилено';
                    else echo 'Очікує підтвердження'; 
                ?>
            </td>
            <td>
                <?php if ($ad['status'] == 'pending') { ?>
                    <a href="admin.php?approve=<?php echo $ad['id']; ?>">Підтвердити</a> |
                    <a href="admin.php?reject=<?php echo $ad['id']; ?>">Відхилити</a> |
                <?php } ?>
                <a href="admin.php?delete=<?php echo $ad['id']; ?>">Видалити</a>
            </td>
            <td>
                <a href="ad.php?id=<?php echo $ad['id']; ?>">Переглянути</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
