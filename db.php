<?php
$host = 'localhost';
$db   = 'expense_tracker';
$user = 'root';      // Стандартний логін для XAMPP/OpenServer
$pass = '';          // Стандартний пароль (часто порожній)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Помилка підключення: " . $e->getMessage());
}
?>