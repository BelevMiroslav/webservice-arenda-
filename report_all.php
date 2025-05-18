<?php
include 'config.php';
session_start();

if ($_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

if (isset($_GET['export']) && $_GET['export'] == 'pdf') {
    require_once 'dompdf/autoload.inc.php';

    $dompdf = new \Dompdf\Dompdf([
        'defaultFont' => 'DejaVu Sans',
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'tempDir' => __DIR__ . '/tmp'
    ]);

    $query = "SELECT * FROM apartments ORDER BY status DESC, id DESC";
    $result = $conn->query($query);

    $html = '
    <!DOCTYPE html>
    <html lang="uk">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            body { 
                font-family: DejaVu Sans, sans-serif; 
                font-size: 7pt;  /* Зменшуємо розмір шрифту */
                margin: 0;
                padding: 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                page-break-inside: auto; /* Розрив таблиці на сторінках */
            }
            th, td {
                border: 1px solid #ddd;
                padding: 4px;  /* Зменшуємо відступи */
                text-align: left;
                line-height: 1.3;
            }
            th {
                background-color: #f2f2f2;
                font-size: 6pt;  /* Менший розмір для заголовків */
                padding: 3px;
            }
            .compact {
                max-width: 50px;  /* Фіксована ширина для вузьких колонок */
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .small-text {
                font-size: 6pt;
            }
        </style>
    </head>
    <body>
        <h1 style="text-align: center; font-size: 10pt; margin-bottom: 5px;">Повний звіт про оголошення</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Адреса</th>
                    <th>Район</th>
                    <th>Кімнати</th>
                    <th>Ліжка</th>
                    <th>Ціна (грн)</th>
                    <th>Площа (м²)</th>
                    <th>Тип будинку</th>
                    <th>Поверх</th>
                    <th>Меблі</th>
                    <th>Ремонт</th>
                    <th>Кухня (м²)</th>
                    <th>Рієлтор</th>
                    <th>Опис</th>
                    <th>Статус</th>
                    <th>Дата відхилення</th>
                </tr>
            </thead>
            <tbody>';

    while ($ad = $result->fetch_assoc()) {
        $html .= '
                <tr>
                    <td>' . $ad['id'] . '</td>
                    <td>' . htmlspecialchars($ad['address']) . '</td>
                    <td>' . htmlspecialchars($ad['district']) . '</td>
                    <td>' . $ad['rooms'] . '</td>
                    <td>' . $ad['beds'] . '</td>
                    <td>' . number_format($ad['price'], 0, '', ' ') . '</td>
                    <td>' . $ad['area'] . '</td>
                    <td>' . htmlspecialchars($ad['house_type']) . '</td>
                    <td>' . $ad['floor'] . '</td>
                    <td>' . ($ad['furniture'] ? 'Так' : 'Ні') . '</td>
                    <td>' . htmlspecialchars($ad['repair']) . '</td>
                    <td>' . $ad['kitchen_area'] . '</td>
                    <td>' . ($ad['realtor'] ? 'Так' : 'Ні') . '</td>
                    <td class="small-text">' . htmlspecialchars($ad['description']) . '</td>
                    <td>';
        
        switch ($ad['status']) {
            case 'approved': $html .= 'Підтверджено'; break;
            case 'rejected': $html .= 'Відхилено'; break;
            default: $html .= 'Очікує';
        }
        
        $html .= '</td>
                    <td>' . ($ad['rejected_at'] ?? '-') . '</td>
                </tr>';
    }

    $html .= '
            </tbody>
        </table>
        <p style="text-align: right; margin-top: 20px;">Згенеровано: ' . date('d.m.Y H:i') . '</p>
    </body>
    </html>';

    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("full_report_" . date('Y-m-d') . ".pdf", ["Attachment" => true]);
    exit();
}

$query = "SELECT * FROM apartments ORDER BY status DESC, id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Звіт про всі оголошення</title>
</head>
<body>
    <h1>Звіт про всі оголошення</h1>
    <a href="?export=pdf" class="btn-report">Експортувати у PDF</a>
    <table cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Адреса</th>
                <th>Район</th>
                <th>Кімнати</th>
                <th>Ліжка</th>
                <th>Ціна (грн)</th>
                <th>Площа (м²)</th>
                <th>Тип будинку</th>
                <th>Поверх</th>
                <th>Меблі</th>
                <th>Ремонт</th>
                <th>Площа кухні (м²)</th>
                <th>Рієлтор</th>
                <th>Опис</th>
                <th>Статус</th>
                <th>Дата відхилення</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($ad = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $ad['id']; ?></td>
                <td><?php echo htmlspecialchars($ad['address']); ?></td>
                <td><?php echo htmlspecialchars($ad['district']); ?></td>
                <td><?php echo $ad['rooms']; ?></td>
                <td><?php echo $ad['beds']; ?></td>
                <td><?php echo $ad['price']; ?></td>
                <td><?php echo $ad['area']; ?></td>
                <td><?php echo htmlspecialchars($ad['house_type']); ?></td>
                <td><?php echo $ad['floor']; ?></td>
                <td><?php echo $ad['furniture'] ? 'Так' : 'Ні'; ?></td>
                <td><?php echo htmlspecialchars($ad['repair']); ?></td>
                <td><?php echo $ad['kitchen_area']; ?></td>
                <td><?php echo $ad['realtor'] ? 'Так' : 'Ні'; ?></td>
                <td><?php echo htmlspecialchars($ad['description']); ?></td>
                <td>
                    <?php 
                        if ($ad['status'] == 'approved') echo 'Підтверджено';
                        elseif ($ad['status'] == 'rejected') echo 'Відхилено';
                        else echo 'Очікує підтвердження'; 
                    ?>
                </td>
                <td><?php echo $ad['rejected_at']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="admin.php" class="btn-report">Назад до адмінпанелі</a>
</body>
</html>
