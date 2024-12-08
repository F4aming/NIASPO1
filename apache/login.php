<?php
session_start(); // Начинаем сессию для хранения информации о пользователе

// Данные для подключения к базе данных
$servername = "db";
$username = "root";
$password = "rootpassword";
$dbname = "restaurant_orders";

// Создаем подключение к базе данных
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверяем подключение
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Переменные для ошибок
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем логин из формы
    $input_username = $conn->real_escape_string($_POST['username']);

    // SQL запрос для поиска пользователя с указанным логином
    $sql = "SELECT * FROM users WHERE username = '$input_username'";
    $result = $conn->query($sql);

    // Проверка наличия пользователя
    if ($result->num_rows > 0) {
        // Получаем данные пользователя из базы
        $user = $result->fetch_assoc();

        // Успешный вход, сохраняем информацию о пользователе в сессии
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php"); // Перенаправляем на главную страницу
        exit;
    } else {
        // Если пользователь с таким логином не найден
        $error = "Пользователь не найден.";
    }
}

$conn->close(); // Закрываем соединение с базой данных
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .login-container {
            width: 300px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-container input[type="text"], .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .register-link {
            display: block;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Вход в систему</h2>

        <?php
        if (!empty($error)) {
            echo "<div class='error'>$error</div>";
        }
        ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Логин" required>
            <!-- Убираем поле для пароля -->
            <button type="submit">Войти</button>
        </form>

        <a href="register.php" class="register-link">Нет аккаунта? Зарегистрируйтесь</a>
    </div>

</body>
</html>
