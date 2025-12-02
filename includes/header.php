<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFinance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg bg-white shadow-sm mb-4 py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="#">
            <i class="bi bi-wallet2 text-primary me-2"></i>MyFinance
        </a>
        
        <div class="d-flex align-items-center gap-3">
            <div class="d-none d-md-block text-end me-2">
                <small class="text-muted d-block" style="font-size: 0.75rem">Загальний баланс</small>
                <span id="totalBalance" class="total-badge" style="font-size: 1.2rem;">...</span>
            </div>

            <select id="currencySelector" class="form-select currency-switch border-0 bg-light" style="width: auto;" onchange="changeCurrency()">
                <option value="UAH" selected>₴ UAH</option>
                <option value="USD">$ USD</option>
                <option value="EUR">€ EUR</option>
                <option value="GBP">£ GBP</option>
            </select>
            <!-- <button class="btn btn-link theme-toggle text-decoration-none me-2" onclick="toggleTheme()" id="themeBtn">
                <i class="bi bi-moon-stars-fill"></i>
            </button>

            <a href="login.php" class="btn btn-light rounded-circle" ...>             -->
            <a href="login.php" class="btn btn-light rounded-circle" style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-box-arrow-right text-danger"></i>
            </a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="d-flex justify-content-end mb-3">
         <span class="badge bg-white text-dark shadow-sm fw-normal px-3 py-2" id="rateInfo">
            <i class="bi bi-arrow-repeat me-1 spinner-border spinner-border-sm"></i> Завантаження курсів...
         </span>
    </div>