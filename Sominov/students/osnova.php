<?php
include 'db.php';
session_start();

// Проверяем, установлена ли сессия
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = trim($_SESSION['username']); // Получаем имя пользователя из сессии и обрезаем пробелы

// Подготовленный запрос для защиты от SQL-инъекций
$query = "SELECT e.id, e.summ, e.category, e.data, e.username_id, u.username 
          FROM expenses e 
          JOIN user u ON e.username_id = u.id
          WHERE u.username = ?"; 

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    die("Ошибка подготовки запроса: " . mysqli_error($conn));
}

// Привязываем параметры
mysqli_stmt_bind_param($stmt, "s", $username);

// Выполняем запрос
mysqli_stmt_execute($stmt);

// Получаем результат
$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    die("Ошибка выполнения запроса: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Основа - Данные расходов</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h2>Список расходов</h2>
    
    <!-- Таблица для отображения данных из expenses -->
    <table class="expense-table" border="1" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Сумма</th>
                <th>Категория</th>
                <th>Дата</th>
                <th>Пользователь</th>
                <th>Редактировать</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['summ']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['data']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td>
                        <a href="edit_expense.php?id=<?php echo htmlspecialchars($row['id']); ?>">Редактировать</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Нет данных для отображения.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
