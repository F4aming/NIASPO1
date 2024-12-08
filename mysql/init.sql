CREATE DATABASE IF NOT EXISTS restaurant_orders;
USE restaurant_orders;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- ID пользователя, сделавшего заказ
    customer_name VARCHAR(255) NOT NULL, -- Имя заказчика
    order_item VARCHAR(255) NOT NULL, -- Наименование блюда
    quantity INT NOT NULL, -- Количество порций
    status VARCHAR(20) DEFAULT 'новый', -- Статус заказа (например, "новый", "готов", "отменён")
    ready_at TIMESTAMP NULL DEFAULT NULL, -- Время, когда заказ будет готов
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Время создания заказа
);

