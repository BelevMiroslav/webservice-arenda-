<?php 
include 'config.php';
include 'header.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM apartments WHERE id = $id");
$row = $result->fetch_assoc();

// Отримання даних про користувача
$userId = $row['user_id'];
$userResult = $conn->query("SELECT *, 
    (SELECT AVG(rating) FROM reviews WHERE user_id = '$userId') AS avg_rating
    FROM users WHERE id = '$userId'");
$user = $userResult->fetch_assoc();

$reviewsResult = $conn->query("SELECT r.rating, r.comment, r.created_at, u.name AS reviewer_name 
                                FROM reviews r 
                                JOIN users u ON r.reviewer_id = u.id 
                                WHERE r.user_id = '$userId'");

// Обробка форми відгуків
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_review'])) {
    $rating = (int)$_POST['rating'];
    $comment = $conn->real_escape_string($_POST['comment']);
    $reviewer_id = $_SESSION['user_id'];

    $insertQuery = "INSERT INTO reviews (user_id, reviewer_id, rating, comment, created_at) 
                    VALUES ('$userId', '$reviewer_id', '$rating', '$comment', NOW())";

    if ($conn->query($insertQuery)) {
        header("Location: ad.php?id=$id");
        exit();
    } else {
        echo "Помилка додавання відгуку: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="dist/css/lightgallery-bundle.min.css">
    <title><?php echo $row['address']; ?></title>
</head>
<body>
    <div class="apartment-details">
        <h1><?php echo $row['address']; ?></h1>
        <div class="price"><?php echo number_format($row['price'], 2) . ' грн'; ?></div>

        <div class="main-content">
            <div class="left-column">
                <div class="image-gallery" id="lightgallery">
                    <a href="uploads/<?php echo $row['photo1']; ?>">
                        <img src="uploads/<?php echo $row['photo1']; ?>" alt="Фото 1">
                    </a>
                    <a href="uploads/<?php echo $row['photo2']; ?>">
                        <img src="uploads/<?php echo $row['photo2']; ?>" alt="Фото 2">
                    </a>
                    <a href="uploads/<?php echo $row['photo3']; ?>">
                        <img src="uploads/<?php echo $row['photo3']; ?>" alt="Фото 3">
                    </a>
                </div>
            </div>

            <div class="right-column">
                <div class="description">
                    <h3>Опис оголошення:</h3>
                    <p><?php echo $row['description']; ?></p>

                    <?php if ($_SESSION['user_id'] === $row['user_id']): ?>
                        <div class="edit-buttons">
                            <a href="edit_ad.php?id=<?php echo $id; ?>" class="btn btn-edit">Редагувати</a>
                            <form action="delete_ad.php" method="POST" onsubmit="return confirm('Ви впевнені, що хочете видалити це оголошення?');" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <button type="submit" class="btn btn-delete">Видалити</button>
                            </form>
                        </div>
                    <?php endif; ?>


                <h2>Інформація про власника:</h2>
                <div class="owner-info">
                    <div class="owner-avatar">
                        <?php if ($user['photo']): ?>
                            <img src="<?php echo $user['photo']; ?>" alt="Фото профілю">
                        <?php else: ?>
                            <div class="question-mark">?</div>
                        <?php endif; ?>
                    </div>
                    <p><strong>Прізвище, ім'я:</strong> <?php echo $user['surname'] . ' ' . $user['name']; ?></p>
                    <p><strong>Рейтинг:</strong> <?php echo $user['avg_rating'] ? number_format($user['avg_rating'], 1) : 'Немає відгуків'; ?> / 5</p>
                    <p><strong>Основний телефон:</strong> <?php echo $user['phone_main']; ?></p>
                        <?php if ($user['phone_extra1']): ?>
                            <p><strong>Додатковий телефон 1:</strong> <?php echo $user['phone_extra1']; ?></p>
                        <?php endif; ?>
                        <?php if ($user['phone_extra2']): ?>
                            <p><strong>Додатковий телефон 2:</strong> <?php echo $user['phone_extra2']; ?></p>
                        <?php endif; ?>
                    <p><strong>На сайті з:</strong> <?php echo date('d.m.Y', strtotime($user['created_at'])); ?></p>
                    <?php if (!empty($user['telegram'])): ?>
                        <p><strong>Телеграм:</strong> <a href="https://t.me/<?php echo $user['telegram']; ?>" target="_blank"><?php echo $user['telegram']; ?></a></p>
                    <?php endif; ?>
                </div>

                <h3>Відгуки про власника:</h3>
                <?php if ($reviewsResult->num_rows > 0): ?>
                    <div class="reviews-container">
                        <?php while ($review = $reviewsResult->fetch_assoc()): ?>
                            <div class="review-block">
                                <div class="review-header">
                                    <span class="reviewer-name"><?php echo $review['reviewer_name']; ?></span>
                                    <span class="review-rating">Рейтинг: <?php echo $review['rating']; ?> / 5</span>
                                </div>
                                <p class="review-comment"><?php echo $review['comment']; ?></p>
                                <div class="review-footer">
                                    <span class="review-date">
                                        <?php echo date('d F Y', strtotime($review['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p>Відгуків про цього користувача поки немає.</p>
                <?php endif; ?>

                <h3>Додати відгук:</h3>
                <form action="ad.php?id=<?php echo $id; ?>" method="POST">
                    <label for="rating">Рейтинг:</label>
                    <select name="rating" id="rating" required>
                        <option value="5">5 - Відмінно</option>
                        <option value="4">4 - Добре</option>
                        <option value="3">3 - Задовільно</option>
                        <option value="2">2 - Погано</option>
                        <option value="1">1 - Жахливо</option>
                    </select>
                    <label for="comment">Коментар:</label>
                    <textarea name="comment" id="comment" rows="4" required></textarea>
                    <button type="submit" name="add_review">Додати відгук</button>
                </form>
            </div>
        </div>
    </div>

    <script src="dist/lightgallery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lightGallery(document.getElementById('lightgallery'));
        });
    </script>
</body>
</html>
