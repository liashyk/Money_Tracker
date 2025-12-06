<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

// --- 1. Створення гаманця ---
if ($action == 'add_wallet' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $balance = $_POST['balance'];
    $color = $_POST['color'];

    $stmt = $pdo->prepare("INSERT INTO wallets (user_id, name, balance, color) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $name, $balance, $color]);
    echo json_encode(['success' => true]);
    exit;
}

// --- 2. Отримання даних для панелі ---
if ($action == 'get_dashboard') {
    $stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $wallets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = 0;
    foreach ($wallets as $w) {
        $total += $w['balance'];
    }

    echo json_encode(['wallets' => $wallets, 'total' => $total]);
    exit;
}

// --- 3. Додавання транзакції (з оновленням балансу) ---
if ($action == 'add_transaction' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $wallet_id = $_POST['wallet_id'];
    $type = $_POST['type']; // 'income' або 'expense'
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $date = $_POST['date'];

    try {
        $pdo->beginTransaction();

        // Запис в історію
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, wallet_id, type, amount, category, date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $wallet_id, $type, $amount, $category, $date]);

        // Оновлення балансу гаманця
        if ($type == 'income') {
            $upd = $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE id = ? AND user_id = ?");
        } else {
            $upd = $pdo->prepare("UPDATE wallets SET balance = balance - ? WHERE id = ? AND user_id = ?");
        }
        $upd->execute([$amount, $wallet_id, $user_id]);

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// --- 4. Статистика для графіків ---
if ($action == 'get_stats') {
    // Витрати за категоріями
    $stmt = $pdo->prepare("SELECT category, SUM(amount) as total FROM transactions WHERE user_id = ? AND type = 'expense' GROUP BY category");
    $stmt->execute([$user_id]);
    $cat_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Доходи проти Витрат
    $stmt = $pdo->prepare("SELECT type, SUM(amount) as total FROM transactions WHERE user_id = ? GROUP BY type");
    $stmt->execute([$user_id]);
    $type_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['categories' => $cat_stats, 'types' => $type_stats]);
    exit;
}

// --- 5. Редагування гаманця ---
if ($action == 'update_wallet' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $balance = $_POST['balance'];
    $color = $_POST['color'];

    // Оновлюємо, але ОБОВ'ЯЗКОВО перевіряємо user_id для безпеки
    $stmt = $pdo->prepare("UPDATE wallets SET name = ?, balance = ?, color = ? WHERE id = ? AND user_id = ?");
    $result = $stmt->execute([$name, $balance, $color, $id, $user_id]);

    echo json_encode(['success' => $result]);
    exit;
}

// --- 6. Видалення гаманця (Бонус: щоб можна було видалити старий) ---
if ($action == 'delete_wallet' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM wallets WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    echo json_encode(['success' => true]);
    exit;
}

// --- 5. Статистика по днях (останні 30 днів) ---
if ($action == 'get_daily_stats') {
    $stmt = $pdo->prepare("
        SELECT date, SUM(amount) as total 
        FROM transactions 
        WHERE user_id = ? 
          AND type = 'expense' 
          AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY date 
        ORDER BY date ASC
    ");
    $stmt->execute([$user_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}
?>