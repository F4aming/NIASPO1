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
    <title>Заказы ресторана</title>
</head>
<body>
    <h1>Система заказов ресторана</h1>
    
    <h2>Добавить новый заказ</h2>
    <form method="POST">
        <input type="text" name="customer_name" placeholder="Имя клиента" required><br><br>
        <input type="text" name="order_item" placeholder="Наименование блюда" required><br><br>
        <input type="number" name="quantity" placeholder="Количество" min="1" required><br><br>
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
                 
                 // Форма для удаления заказа
                 . "<form method='POST' style='display:inline;'>"
                 . "<input type='hidden' name='delete_order_id' value='" . $row['id'] . "'>"
                 . "<button type='submit'>Удалить заказ</button>"
                 . "</form><br>"
                 
                 // Форма для обновления количества заказа
                 . "<form method='POST' style='display:inline;'>"
                 . "<input type='hidden' name='update_order_id' value='" . $row['id'] . "'>"
                 . "<button type='submit'>Обновить статус</button>"
                 . "</form>"
                 . "</li><br>";
        }
    } else {
        echo "Нет заказов.";
    }
    $conn->close();
    ?>
    </ul>
</body>
</html>
