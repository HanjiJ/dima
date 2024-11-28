<?php
include 'db.php';
if (!$conn) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

$error = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Подготовленный запрос для безопасности
    $query = "SELECT * FROM user WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Привязываем параметры и выполняем запрос
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            // Если логин и пароль совпадают, создаем сессию
            session_start();
            $_SESSION['username'] = $username;
            header("Location: osnova.php");
            exit();
        } else {
            // Неверный логин или пароль
            $error = "Неверный логин или пароль.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $error = "Ошибка при выполнении запроса.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="image-container">
        <img src="images/logo.png" alt="Логотип магазина" class="center-image" width="150" height="150">
    </div>
    
    <form method="post" action="login.php">
        <h2>Вход</h2>
        <input type="text" name="username" placeholder="Имя пользователя" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти</button>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
