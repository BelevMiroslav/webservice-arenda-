<?php
include 'connect.php';

$query = "DELETE FROM apartments WHERE status = 'rejected' AND rejected_at <= NOW() - INTERVAL 3 DAY";

if ($conn->query($query)) {
    echo "Видалені записи зі статусом 'rejected', старіші за три дні.";
} else {
    echo "Помилка видалення: " . $conn->error;
}

$conn->close();
?>
