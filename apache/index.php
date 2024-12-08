<?php

$servername = "db";
$username = "root";
$password = "rootpassword";
$dbname = "restaurant_orders";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Удаление заказа
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_order_id'])) {
    $order_id = (int)$_POST['delete_order_id'];
    
    // Удаляем заказ из базы данных
    $sql = "DELETE FROM orders WHERE id = $order_id";
    
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
    
    $sql = "UPDATE orders SET quantity = $new_quantity WHERE id = $order_id";
    
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
    
    $sql = "INSERT INTO orders (customer_name, order_item, quantity) VALUES ('$customer_name', '$order_item', $quantity)";
    
    if ($conn->query($sql) === TRUE) {
        echo "Новый заказ добавлен!";
    } else {
        echo "Ошибка добавления: " . $conn->error;
    }
}

// Получение списка заказов
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система заказов ресторана</title>
    <style>
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
        .registration-button {
            position: absolute;
            top: 10px;
            right: 10px;
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
        
        .registration-button:hover {
            background-color: #4CAF50;
            color: #ffffff;
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

        /* Всплывающее окно */
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
        <button class="registration-button" onclick="openModal()">Регистрация</button>
    </header>
    <main>
    <h2>Меню</h2>
        <ul>
            <li class="menu-item">
                <img src="https://cdnn21.img.ria.ru/images/98976/61/989766135_0:105:2000:1230_650x0_80_0_0_a0c8ea5459a0ba08e5e4d558e2b19ad3.jpg.webp" 
                    alt="Пицца" 
                    data-name="Пицца" 
                    data-quantity="1" 
                    onclick="addToOrder(this)">
                <div>
                    <strong>Название блюда: Пицца</strong><br>
                    <small>Цена: 500 рублей</small>
                </div>
            </li>
            <li class="menu-item">
                <img src="https://static.sushiwok.ru/img/a0e48307a71a9a041c11822a2ccfd4b8" 
                    alt="Суши" 
                    data-name="Суши" 
                    data-quantity="1" 
                    onclick="addToOrder(this)">
                <div>
                    <strong>Название блюда: Суши</strong><br>
                    <small>Цена: 300 рублей</small>
                </div>
            </li>
            <li class="menu-item">
                <img src="https://chefrestoran.ru/wp-content/uploads/2022/04/stejk-1024x768.jpeg" 
                    alt="Стейк" 
                    data-name="Стейк" 
                    data-quantity="1" 
                    onclick="addToOrder(this)">
                <div>
                    <strong>Название блюда: Стейк</strong><br>
                    <small>Цена: 1200 рублей</small>
                </div>
            </li>
        </ul>

<!-- Скрытая форма для отправки данных -->
        <form id="addOrderForm" method="POST" style="display: none;">
            <input type="hidden" name="customer_name" value="Гость">
            <input type="hidden" name="order_item" id="order_item" value="">
            <input type="hidden" name="quantity" id="quantity" value="1">
        </form>


        <h2>Добавить новый заказ</h2>
        <form method="POST">
            <input type="text" name="customer_name" placeholder="Имя клиента" required>
            <input type="text" name="order_item" placeholder="Наименование блюда" required>
            <input type="number" name="quantity" placeholder="Количество" min="1" required>
            <button type="submit">Добавить заказ</button>
        </form>

        <h2>История заказов</h2>
        <ul>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<li><strong>Имя клиента: " . $row['customer_name'] . "</strong><br>"
                     . "Блюдо: " . $row['order_item'] . "<br>"
                     . "Количество: " . $row['quantity'] . "<br>"
                     . "Статус: " . $row['status'] . "<br>"
                     . "<small>Добавлено: " . $row['created_at'] . "</small><br>"
                     
                     . "<div class='action-buttons'>"
                     // Форма для удаления заказа
                     . "<form method='POST'>"
                     . "<input type='hidden' name='delete_order_id' value='" . $row['id'] . "'>"
                     . "<button type='submit'>Удалить заказ</button>"
                     . "</form>"
                     
                     // Форма для обновления количества заказа
                     . "<form method='POST'>"
                     . "<input type='hidden' name='update_order_id' value='" . $row['id'] . "'>"
                     . "Новое количество: <input type='number' name='new_quantity' value='" . $row['quantity'] . "' min='1' required>"
                     . "<button type='submit' class='update'>Обновить заказ</button>"
                     . "</form>"
                     . "</div>"
                     . "</li>";
            }
        } else {
            echo "<p>Нет заказов.</p>";
        }
        $conn->close();
        ?>
        </ul>
    </main>

    <!-- Всплывающее окно -->
    <div id="registrationModal" class="modal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal()">X</button>
            <h3>Регистрация</h3>
            <form>
                <input type="text" name="username" placeholder="Имя пользователя" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Пароль" required>
                <button type="submit">Зарегистрироваться</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('registrationModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('registrationModal').style.display = 'none';
        }
    </script>
</body>
</html>


