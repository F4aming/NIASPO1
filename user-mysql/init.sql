-- Создание базы данных, если она еще не существует
CREATE DATABASE IF NOT EXISTS restaurant_orders;

-- Использование базы данных
USE restaurant_orders;

-- Создание таблицы users, если она еще не существует
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Тестовый пользователь (если нужно добавить сразу одного пользователя)
INSERT INTO users (username, email, password_hash) VALUES
('admin', 'admin@example.com', 'testpassword');
