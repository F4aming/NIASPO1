<?php
session_start();

// Подключение к базе данных
$servername = "db";
$username = "root";
$password = "rootpassword";
$dbname = "restaurant_orders";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Создание таблицы заказов, если она не существует
$table_creation_query = "
    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL, -- Связь с пользователем
        customer_name VARCHAR(255) NOT NULL,
        order_item VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        status VARCHAR(20) DEFAULT 'новый', -- Статус заказа
        ready_at TIMESTAMP NULL DEFAULT NULL, -- Время готовности заказа
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Время создания заказа
    );
";


if ($conn->query($table_creation_query) === FALSE) {
    die("Ошибка создания таблицы: " . $conn->error);
}

// Проверяем, авторизован ли пользователь
$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['username']);
$current_user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Удаление заказа
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_order_id'])) {
    $order_id = (int)$_POST['delete_order_id'];
    $sql = "DELETE FROM orders WHERE id = $order_id AND user_id = $current_user_id";
    
    if ($conn->query($sql) === TRUE) {
        echo "Заказ удален!";
    } else {
        echo "Ошибка удаления: " . $conn->error;
    }
}

// Обновление заказа
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_order_id'], $_POST['new_quantity'])) {
    $order_id = (int)$_POST['update_order_id'];
    $new_quantity = (int)$_POST['new_quantity'];
    
    $sql = "UPDATE orders SET quantity = $new_quantity WHERE id = $order_id AND user_id = $current_user_id";
    
    if ($conn->query($sql) === TRUE) {
        echo "Заказ обновлен!";
    } else {
        echo "Ошибка обновления: " . $conn->error;
    }
}

// Добавление нового заказа
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['customer_name'], $_POST['order_item'], $_POST['quantity'])) {
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $order_item = $conn->real_escape_string($_POST['order_item']);
    $quantity = (int)$_POST['quantity'];

    // Получение user_id текущего пользователя
    $current_user_id = $_SESSION['user_id']; // Убедитесь, что в сессии хранится user_id

    $sql = "INSERT INTO orders (user_id, customer_name, order_item, quantity) 
            VALUES ($current_user_id, '$customer_name', '$order_item', $quantity)";

    if ($conn->query($sql) === TRUE) {
        echo "Новый заказ добавлен!";
    } else {
        echo "Ошибка добавления: " . $conn->error;
    }
}

// Получение списка заказов
$sql = "SELECT * FROM orders WHERE user_id = $current_user_id ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система заказов ресторана</title>
    <style>
        /* Стили для кнопок и страницы */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            position: relative;
            text-align: center;
        }

        .registration-button, .login-button {
            position: absolute;
            top: 10px;
            background-color: #ffffff;
            color: #4CAF50;
            border: 2px solid #4CAF50;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .registration-button:hover, .login-button:hover {
            background-color: #4CAF50;
            color: #ffffff;
        }

        .registration-button {
            right: 100px;
        }

        .login-button {
            right: 10px;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .button-container a {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 10px;
            font-size: 16px;
        }

        .button-container a:hover {
            background-color: #45a049;
        }

        main {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1, h2, h3 {
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        form input[type="text"], form input[type="number"], form input[type="password"], form input[type="email"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #45a049;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background: #f9f9f9;
            margin-bottom: 10px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        li img {
            max-width: 100px;
            margin-right: 10px;
            vertical-align: middle;
        }

        li strong {
            color: #333;
        }

        li small {
            color: #666;
        }

        .menu-item {
            display: flex;
            align-items: center;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .modal-content h3 {
            margin-bottom: 20px;
        }

        .close-modal {
            background: red;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>

<header>
    <h1>Система заказов ресторана</h1>
    <div class="button-container">
        <?php if ($is_logged_in): ?>
            <a href="logout.php">Выйти</a>
        <?php else: ?>
            <a href="login.php">Войти</a>
            <a href="register.php">Зарегистрироваться</a>
        <?php endif; ?>
    </div>
</header>

<main>
    <h2>Меню</h2>
    <ul>
        <li class="menu-item">
            <img src="https://cdnn21.img.ria.ru/images/98976/61/989766135_0:105:2000:1230_650x0_80_0_0_a0c8ea5459a0ba08e5e4d558e2b19ad3.jpg.webp" 
                 alt="Пицца" data-name="Пицца" data-quantity="1" onclick="addToOrder(this)">
            <div>
                <strong>Название блюда: Пицца</strong><br>
                <small>Цена: 500 рублей</small>
            </div>
        </li>
        <li class="menu-item">
            <img src="https://static.sushiwok.ru/img/a0e48307a71a9a041c11822a2ccfd4b8" 
                 alt="Суши" data-name="Суши" data-quantity="1" onclick="addToOrder(this)">
            <div>
                <strong>Название блюда: Суши</strong><br>
                <small>Цена: 300 рублей</small>
            </div>
        </li>
    </ul>

    <h2>Ваши заказы</h2>
    <form method="POST" action="">
        <input type="text" name="customer_name" placeholder="Ваше имя" required>
        <input type="text" name="order_item" placeholder="Наименование блюда" required>
        <input type="number" name="quantity" placeholder="Количество" required>
        <button type="submit">Добавить заказ</button>
    </form>

    <h3>Список заказов</h3>
    <?php if ($result->num_rows > 0): ?>
        <ul>
            <?php while($order = $result->fetch_assoc()): ?>
                <li>
                    <strong>Заказчик: <?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                    Блюдо: <?php echo htmlspecialchars($order['order_item']); ?><br>
                    Количество: <?php echo htmlspecialchars($order['quantity']); ?><br>
                    <strong>Статус: <?php echo htmlspecialchars($order['status']); ?></strong><br>
                    <small>Дата создания: <?php echo $order['created_at']; ?></small><br>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="update_order_id" value="<?php echo $order['id']; ?>">
                        <input type="number" name="new_quantity" value="<?php echo $order['quantity']; ?>" required>
                        <button type="submit">Обновить заказ</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete_order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit">Удалить заказ</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Нет заказов</p>
    <?php endif; ?>
</main>

</body>
</html>
<?php
$conn->close();
?>
