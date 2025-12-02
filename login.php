<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        // Вхід
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Невірний пароль";
        }
    } else {
        // Реєстрація
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        if ($stmt->execute([$username, $hash])) {
            $_SESSION['user_id'] = $pdo->lastInsertId();
            
            // Створюємо перший гаманець для нового користувача
            $pdo->prepare("INSERT INTO wallets (user_id, name, balance, color) VALUES (?, 'Готівка', 0, '#198754')")
                ->execute([$_SESSION['user_id']]);
                
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Вхід</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-dark">
    <div class="card p-4" style="width: 320px;">
        <h3 class="text-center">Вхід / Реєстрація</h3>
        <?php if(isset($error)) echo "<div class='alert alert-danger p-1'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-2">
                <input type="text" name="username" class="form-control" placeholder="Логін" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Пароль" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Увійти</button>
        </form>
    </div>
</body>
</html>