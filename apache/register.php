<?php
// Подключение к базе данных
$servername = "db";
$username = "root";
$password = "rootpassword";
$dbname = "restaurant_orders";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Создание таблицы users, если она не существует
$table_creation_query = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
";

if ($conn->query($table_creation_query) === TRUE) {
    // Таблица создана или уже существует
} else {
    die("Ошибка создания таблицы: " . $conn->error);
}

// Обработка регистрации
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Генерация хеша пароля
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // SQL-запрос для вставки данных
    $sql = "INSERT INTO users (username, email, password_hash) VALUES ('$username', '$email', '$password_hash')";

    if ($conn->query($sql) === TRUE) {
        $success_message = "Регистрация успешна!";
    } else {
        $error_message = "Ошибка: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .registration-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .registration-form h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .registration-form input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .registration-form button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .registration-form button:hover {
            background-color: #45a049;
        }

        .message {
            margin-bottom: 10px;
            color: green;
        }

        .error {
            margin-bottom: 10px;
            color: red;
        }
    </style>
</head>
<body>
    <div class="registration-form">
        <h2>Регистрация</h2>
        
        <?php if (!empty($success_message)): ?>
            <p class="message"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="username" placeholder="Имя пользователя" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Зарегистрироваться</button>
        </form>
    </div>
</body>
</html>
