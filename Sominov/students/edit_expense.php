<?php
include 'db.php';
session_start();

// Проверка наличия параметра 'id'
if (!isset($_GET['id'])) {
    die("ID записи не указано.");
}

$id = $_GET['id'];

// Получаем имя пользователя из сессии
$username = $_SESSION['username'];

// Подготовленный запрос для получения данных по указанному ID и пользователю
$query = "SELECT e.id, e.summ, e.category, e.data, e.username_id
          FROM expenses e
          JOIN user u ON e.username_id = u.id
          WHERE e.id = ? AND u.username = ?";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Ошибка подготовки запроса: " . mysqli_error($conn));
}

// Привязываем параметры
mysqli_stmt_bind_param($stmt, "is", $id, $username);

// Выполняем запрос
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("Запись не найдена.");
}

$row = mysqli_fetch_assoc($result);

// Обработка формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete'])) {
        // Если была нажата кнопка удаления, выполняем удаление
        $delete_query = "DELETE FROM expenses WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);

        if (!$delete_stmt) {
            die("Ошибка подготовки запроса для удаления: " . mysqli_error($conn));
        }

        // Привязываем параметр для удаления
        mysqli_stmt_bind_param($delete_stmt, "i", $id);

        // Выполняем запрос
        $delete_result = mysqli_stmt_execute($delete_stmt);

        if ($delete_result) {
            header("Location: osnova.php"); // После успешного удаления перенаправляем на osnova.php
            exit();
        } else {
            $error = "Ошибка при удалении записи.";
        }
    } else {
        // Обновление записи
        $summ = mysqli_real_escape_string($conn, $_POST['summ']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $data = mysqli_real_escape_string($conn, $_POST['data']);

        // Подготовленный запрос для обновления записи
        $update_query = "UPDATE expenses SET summ = ?, category = ?, data = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);

        if (!$update_stmt) {
            die("Ошибка подготовки запроса для обновления: " . mysqli_error($conn));
        }

        // Привязываем параметры для обновления
        mysqli_stmt_bind_param($update_stmt, "sssi", $summ, $category, $data, $id);

        // Выполняем запрос
        $update_result = mysqli_stmt_execute($update_stmt);

        if ($update_result) {
            header("Location: osnova.php"); // После успешного редактирования перенаправляем на osnova.php
            exit();
        } else {
            $error = "Ошибка при обновлении данных.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование расхода</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h2>Редактирование расхода</h2>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="edit_expense.php?id=<?php echo htmlspecialchars($row['id']); ?>">
        <label for="summ">Сумма:</label>
        <input type="number" name="summ" value="<?php echo htmlspecialchars($row['summ']); ?>" required><br><br>

        <label for="category">Категория:</label>
        <input type="text" name="category" value="<?php echo htmlspecialchars($row['category']); ?>" required><br><br>

        <label for="data">Дата:</label>
        <input type="date" name="data" value="<?php echo htmlspecialchars($row['data']); ?>" required><br><br>

        <button type="submit">Обновить</button>
        <button type="submit" name="delete" onclick="return confirm('Вы уверены, что хотите удалить эту запись?')">Удалить</button>
    </form>

</body>
</html>
