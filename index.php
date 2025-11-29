<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Трекер Витрат</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>💰 Мій Трекер</h1>
        <a href="login.php" class="btn btn-outline-danger">Вийти</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow p-3 mb-4">
                <h4>Додати витрату</h4>
                <form id="expenseForm">
                    <div class="mb-2">
                        <input type="number" id="amount" class="form-control" placeholder="Сума (грн)" required>
                    </div>
                    <div class="mb-2">
                        <select id="category" class="form-select">
                            <option value="Їжа">🍔 Їжа</option>
                            <option value="Транспорт">🚌 Транспорт</option>
                            <option value="Житло">🏠 Житло</option>
                            <option value="Розваги">🎬 Розваги</option>
                            <option value="Інше">📦 Інше</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <input type="date" id="date" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Зберегти</button>
                </form>
            </div>
            
            <div class="card shadow p-3">
                <canvas id="myChart"></canvas>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow p-3">
                <h4>Історія витрат</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Категорія</th>
                            <th>Сума</th>
                        </tr>
                    </thead>
                    <tbody id="expenseList">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Завантаження даних при старті
    document.addEventListener("DOMContentLoaded", () => {
        loadExpenses();
        loadChart();
        document.getElementById('date').valueAsDate = new Date(); // Сьогоднішня дата
    });

    // 2. Обробка форми
    document.getElementById('expenseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('amount', document.getElementById('amount').value);
        formData.append('category', document.getElementById('category').value);
        formData.append('date', document.getElementById('date').value);

        fetch('api.php?action=add', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                loadExpenses();
                loadChart();
                e.target.reset();
                document.getElementById('date').valueAsDate = new Date();
            }
        });
    });

    // 3. Функція завантаження списку
    function loadExpenses() {
        fetch('api.php?action=list')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('expenseList');
            tbody.innerHTML = '';
            data.forEach(item => {
                tbody.innerHTML += `
                    <tr>
                        <td>${item.date}</td>
                        <td>${item.category}</td>
                        <td>${item.amount} грн</td>
                    </tr>
                `;
            });
        });
    }

    // 4. Побудова графіка (Chart.js)
    let myChart = null;

    function loadChart() {
        fetch('api.php?action=stats')
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('myChart').getContext('2d');
            const labels = data.map(d => d.category);
            const values = data.map(d => d.total);

            if (myChart) myChart.destroy(); // Оновлюємо графік

            myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                    }]
                }
            });
        });
    }
</script>
</body>
</html>