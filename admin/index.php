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
        echo "Order deleted!";
    } else {
        echo "Error deleting: " . $conn->error;
    }
}

// Обновление статуса заказа на "готов" и запись времени
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status_id'], $_POST['new_status'])) {
    $order_id = (int)$_POST['update_status_id'];
    $new_status = $conn->real_escape_string($_POST['new_status']);

    // Если статус изменяется на "готов", записываем время в колонку ready_at
    if ($new_status == 'ready') {
        $ready_at = date("Y-m-d H:i:s");
        $sql = "UPDATE orders SET status = '$new_status', ready_at = '$ready_at' WHERE id = $order_id";
    } else {
        // Если статус не "готов", очищаем колонку ready_at
        $sql = "UPDATE orders SET status = '$new_status', ready_at = NULL WHERE id = $order_id";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Order status updated!";
    } else {
        echo "Error updating: " . $conn->error;
    }
}

// Удаление заказов, которые находятся в статусе "готов" и прошла 1 минута
$sql_delete_ready = "DELETE FROM orders WHERE status = 'ready' AND ready_at IS NOT NULL AND TIMESTAMPDIFF(SECOND, ready_at, NOW()) > 60";
$conn->query($sql_delete_ready);

// Получение всех заказов
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $conn->query($sql);

// Получение готовых заказов
$sql_ready = "SELECT * FROM orders WHERE status = 'ready' ORDER BY created_at DESC";
$result_ready = $conn->query($sql_ready);

// Получение отменённых заказов
$sql_cancelled = "SELECT * FROM orders WHERE status = 'cancelled' ORDER BY created_at DESC";
$result_cancelled = $conn->query($sql_cancelled);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Restaurant Orders</title>
</head>
<body>
    <h1>Admin Panel - Restaurant Orders</h1>
    
    <h2>Order List</h2>

    <table border="1">
        <thead>
            <tr>
                <th>Orders in Process</th>
                <th>Ready Orders</th>
                <th>Cancelled Orders</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <ul>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            if ($row['status'] !== 'ready' && $row['status'] !== 'cancelled') {
                                echo "<li><strong>Customer Name: " . $row['customer_name'] . "</strong><br>"
                                     . "Dish: " . $row['order_item'] . "<br>"
                                     . "Quantity: " . $row['quantity'] . "<br>"
                                     . "Status: " . $row['status'] . "<br>"
                                     . "<small>Added: " . $row['created_at'] . "</small><br>"
                                     . "<form method='POST' style='display:inline;'>"
                                     . "<input type='hidden' name='update_status_id' value='" . $row['id'] . "'>"
                                     . "<select name='new_status'>
                                            <option value='new' " . ($row['status'] == 'new' ? 'selected' : '') . ">New</option>
                                            <option value='ready' " . ($row['status'] == 'ready' ? 'selected' : '') . ">Ready</option>
                                            <option value='cancelled' " . ($row['status'] == 'cancelled' ? 'selected' : '') . ">Cancelled</option>
                                         </select>"
                                     . "<button type='submit'>Update Status</button>"
                                     . "</form>"
                                     . "<form method='POST' style='display:inline;'>"
                                     . "<input type='hidden' name='delete_order_id' value='" . $row['id'] . "'>"
                                     . "<button type='submit'>Delete Order</button>"
                                     . "</form></li>";
                            }
                        }
                    } else {
                        echo "No orders.";
                    }
                    ?>
                    </ul>
                </td>
                
                <td>
                    <ul>
                    <?php
                    if ($result_ready->num_rows > 0) {
                        while($row = $result_ready->fetch_assoc()) {
                            echo "<li><strong>Customer Name: " . $row['customer_name'] . "</strong><br>"
                                 . "Dish: " . $row['order_item'] . "<br>"
                                 . "Quantity: " . $row['quantity'] . "<br>"
                                 . "Status: " . $row['status'] . "<br>"
                                 . "<small>Added: " . $row['created_at'] . "</small><br>"
                                 . "<form method='POST' style='display:inline;'>"
                                 . "<input type='hidden' name='delete_order_id' value='" . $row['id'] . "'>"
                                 . "<button type='submit'>Delete Order</button>"
                                 . "</form></li>";
                        }
                    } else {
                        echo "No ready orders.";
                    }
                    ?>
                    </ul>
                </td>

                <td>
                    <ul>
                    <?php
                    if ($result_cancelled->num_rows > 0) {
                        while($row = $result_cancelled->fetch_assoc()) {
                            echo "<li><strong>Customer Name: " . $row['customer_name'] . "</strong><br>"
                                 . "Dish: " . $row['order_item'] . "<br>"
                                 . "Quantity: " . $row['quantity'] . "<br>"
                                 . "Status: " . $row['status'] . "<br>"
                                 . "<small>Added: " . $row['created_at'] . "</small><br>"
                                 . "<form method='POST' style='display:inline;'>"
                                 . "<input type='hidden' name='delete_order_id' value='" . $row['id'] . "'>"
                                 . "<button type='submit'>Delete Order</button>"
                                 . "</form></li>";
                        }
                    } else {
                        echo "No cancelled orders.";
                    }
                    ?>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>

    <?php $conn->close(); ?>
</body>
</html>
