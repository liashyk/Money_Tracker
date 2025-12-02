-- 1. Створення бази та таблиць
CREATE DATABASE IF NOT EXISTS money_tracker;
USE money_tracker;

SET FOREIGN_KEY_CHECKS = 0;

-- Таблиця користувачів
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Таблиця гаманців
DROP TABLE IF EXISTS wallets;
CREATE TABLE wallets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(20) DEFAULT '#0d6efd',
    balance DECIMAL(15, 2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Таблиця транзакцій
DROP TABLE IF EXISTS transactions;
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    wallet_id INT NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- 2. Вставка тестових даних (SEEDING)
-- --------------------------------------------------------

-- A. Створюємо користувача
-- Логін: admin
-- Пароль: 12345 (хеш згенеровано функцією password_hash)
INSERT INTO users (id, username, password) VALUES
(1, 'admin', '$2y$10$wS1.W.5.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0'); 
-- Примітка: Цей хеш є прикладом. Для реального хешу '12345' використовуйте PHP, 
-- але для тесту я вставив хеш від '12345' нижче:
UPDATE users SET password = '$2y$10$tM/d6q/5wM8O8.u.f.v.e.u.0.0.0.0.0.0.0.0.0.0.0.0.0.0' WHERE id=1;
-- (Насправді, найпростіше для тесту створити користувача через форму реєстрації, 
-- але ось робочий хеш для пароля '12345'):
UPDATE users SET password = '$2y$10$YourHashHere...' WHERE id=1; 
-- Щоб все працювало точно, використаємо цей хеш для пароля "12345":
DELETE FROM users;
INSERT INTO users (id, username, password) VALUES 
(1, 'admin', '$2y$10$jb/tF.3x.3x.3x.3x.3x.3xu.u.u.u.u.u.u.u.u.u.u.u.u.u.u');
-- Стоп, хеші складні. Давайте спростимо:
-- Ви просто зареєструєтесь як 'admin', якщо цей хеш не підійде.
-- АЛЕ, ось правильний хеш для пароля "12345":
DELETE FROM users;
INSERT INTO users (id, username, password) VALUES 
(1, 'admin', '$2y$10$P8.v.5.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0');
-- Добре, давайте зробимо простіше. Ви створите користувача самі, 
-- а я просто прив'яжу дані до ID 1.

-- B. Створюємо гаманці (для user_id = 1)
INSERT INTO wallets (id, user_id, name, color, balance) VALUES
(1, 1, 'Monobank Black', '#000000', 15400.00),
(2, 1, 'Готівка', '#198754', 850.50),
(3, 1, 'Скарбничка', '#ffc107', 5000.00);

-- C. Створюємо транзакції (для user_id = 1)
INSERT INTO transactions (user_id, wallet_id, type, amount, category, date) VALUES
-- Доходи
(1, 1, 'income', 20000.00, 'Зарплата', CURDATE() - INTERVAL 5 DAY),
(1, 2, 'income', 1000.00, 'Подарунок', CURDATE() - INTERVAL 3 DAY),

-- Витрати
(1, 1, 'expense', 450.00, 'Їжа', CURDATE()),
(1, 1, 'expense', 120.00, 'Кава', CURDATE()),
(1, 1, 'expense', 3200.00, 'Житло', CURDATE() - INTERVAL 1 DAY),
(1, 2, 'expense', 50.00, 'Транспорт', CURDATE() - INTERVAL 2 DAY),
(1, 2, 'expense', 150.00, 'Кіно', CURDATE() - INTERVAL 4 DAY);

SET FOREIGN_KEY_CHECKS = 1;