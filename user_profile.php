<?php
include 'config.php';
include 'header.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "
    SELECT 
        *, 
        TIMESTAMPDIFF(DAY, created_at, NOW()) AS days_on_site,
        (SELECT AVG(rating) FROM reviews WHERE user_id = '$user_id') AS avg_rating
    FROM users 
    WHERE id = '$user_id'
";
$result = $conn->query($query);
$user = $result->fetch_assoc();
$reviews_query = "SELECT r.rating, r.comment, r.created_at, u.name AS reviewer_name 
                  FROM reviews r 
                  JOIN users u ON r.reviewer_id = u.id 
                  WHERE r.user_id = '$user_id'";
$reviews_result = $conn->query($reviews_query);

$ads_query = "SELECT id, address, price, rooms, area, status, photo1 
              FROM apartments
              WHERE user_id = '$user_id' AND status = 'approved'";
$ads_result = $conn->query($ads_query);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Профіль користувача</title>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="avatar">
                <?php if ($user['photo']): ?>
                    <img src="<?php echo $user['photo']; ?>" alt="Фото профілю">
                <?php else: ?>
                    <div class="question-mark">?</div>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h2><?php echo "{$user['surname']} {$user['name']} {$user['patronymic']}"; ?></h2>
                <p><strong>Вік:</strong> <?php echo $user['age']; ?></p>
                <p><strong>Стать:</strong> <?php echo $user['gender'] == 'male' ? 'Чоловік' : 'Жінка'; ?></p>
                <p><strong>Основний телефон:</strong> <?php echo $user['phone_main']; ?></p>
                <?php if ($user['phone_extra1']): ?>
                    <p><strong>Додатковий телефон 1:</strong> <?php echo $user['phone_extra1']; ?></p>
                <?php endif; ?>
                <?php if ($user['phone_extra2']): ?>
                    <p><strong>Додатковий телефон 2:</strong> <?php echo $user['phone_extra2']; ?></p>
                <?php endif; ?>
                <?php if ($user['telegram']): ?>
                    <p><strong>Telegram:</strong> <?php echo $user['telegram']; ?></p>
                <?php endif; ?>
                <p><strong>Рейтинг:</strong> <?php echo $user['avg_rating'] ? number_format($user['avg_rating'], 1) : 'Немає відгуків'; ?> / 5</p>
                <p><strong>Днів на сайті:</strong> <?php echo $user['days_on_site']; ?> днів</p>
                <a href="edit_profile.php" class="edit-profile-btn">Редагувати профіль</a>
            </div>
        </div>

        <a href="post_ad.php" class="create-ad-btn">Створити оголошення</a>

        <h2>Оголошення</h2>

        <?php if ($ads_result->num_rows > 0): ?>
            <div class="ads-container">
                <?php while ($ad = $ads_result->fetch_assoc()): ?>
                    <div class="ad-block" onclick="window.location.href='ad.php?id=<?php echo $ad['id']; ?>'">
                        <div class="ad-image">
                            <?php
                            $imagePath = !empty($ad['photo1']) ? 'uploads/' . $ad['photo1'] : 'default-image.jpg';
                            ?>
                            <img src="<?php echo $imagePath; ?>" alt="Фото оголошення">
                        </div>
                        <div class="ad-info">
                            <p class="ad-title"><?php echo $ad['address']; ?></p>
                            <p class="ad-price"><?php echo strtoupper($ad['price']) . ' грн'; ?></p>
                            <p class="ad-details">
                                Кімнат: <?php echo $ad['rooms']; ?> | Площа: <?php echo $ad['area']; ?> м² | Статус: <?php echo ucfirst($ad['status']); ?>
                            </p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Оголошень поки немає.</p>
        <?php endif; ?>

        <h2>Відгуки</h2>
        <?php if ($reviews_result->num_rows > 0): ?>
            <div class="reviews-container">
                <?php while ($review = $reviews_result->fetch_assoc()): ?>
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
            <p>Відгуків поки немає.</p>
        <?php endif; ?>
    </div>
</body>
</html>
