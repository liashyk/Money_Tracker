let currentCurrency = 'UAH';
let rates = { UAH: 1, USD: 1, EUR: 1, GBP: 1 }; 
const symbols = { UAH: '₴', USD: '$', EUR: '€', GBP: '£' };
let globalData = null;
let donutChart = null;
let barChart = null;
let editModalInstance = null;

document.addEventListener("DOMContentLoaded", async () => {
    await fetchRates();
    loadDashboard();
    loadCharts();
    document.getElementById('date').valueAsDate = new Date();
});

// --- API та Валюти ---
async function fetchRates() {
    try {
        const response = await fetch('https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json');
        const data = await response.json();
        const usd = data.find(item => item.cc === 'USD').rate;
        const eur = data.find(item => item.cc === 'EUR').rate;
        const gbp = data.find(item => item.cc === 'GBP').rate;
        rates = { UAH: 1, USD: usd, EUR: eur, GBP: gbp };
        document.getElementById('rateInfo').innerText = `Курс НБУ: 1$ = ${usd.toFixed(2)}₴ | 1€ = ${eur.toFixed(2)}₴`;
    } catch (error) {
        console.error('Помилка НБУ:', error);
        document.getElementById('rateInfo').innerText = 'Офлайн режим (UAH)';
    }
}

function convert(amountUAH) { return (amountUAH / rates[currentCurrency]).toFixed(2); }
function formatMoney(amountUAH) { return parseFloat(convert(amountUAH)).toLocaleString() + ' ' + symbols[currentCurrency]; }

function changeCurrency() {
    currentCurrency = document.getElementById('currencySelector').value;
    document.getElementById('walletCurrencyHint').innerText = `(відображається в ${currentCurrency})`;
    if (globalData) renderWallets(globalData);
    loadCharts();
}

// --- Завантаження даних ---
function loadDashboard() {
    fetch('api.php?action=get_dashboard').then(res => res.json()).then(data => {
        globalData = data;
        renderWallets(data);
    });
}

function renderWallets(data) {
    document.getElementById('totalBalance').innerText = formatMoney(data.total);
    const container = document.getElementById('walletsContainer');
    const select = document.getElementById('walletSelect');
    
    container.innerHTML = '';
    if (select.options.length === 0) select.innerHTML = '';

    data.wallets.forEach(w => {
        container.innerHTML += `
            <div class="col-md-3 col-6 mb-2">
                <div class="card p-3 wallet-card h-100 d-flex flex-column justify-content-between" style="background-color: ${w.color}">
                    <button class="edit-btn" onclick='openEditModal(${JSON.stringify(w)})'><i class="bi bi-pencil-fill"></i></button>
                    <div style="opacity: 0.9">${w.name}</div>
                    <div class="fs-4 fw-bold mt-2">${formatMoney(w.balance)}</div>
                </div>
            </div>`;
        if (select.options.length < data.wallets.length) select.innerHTML += `<option value="${w.id}">${w.name}</option>`;
    });
}

// --- Графіки ---
function loadCharts() {
    fetch('api.php?action=get_stats').then(res => res.json()).then(data => {
        const catData = data.categories.map(d => convert(d.total));
        const catLabels = data.categories.map(d => d.category);
        let inc = 0, exp = 0;
        data.types.forEach(d => {
            if (d.type === 'income') inc = convert(d.total);
            if (d.type === 'expense') exp = convert(d.total);
        });
        drawDonut(catLabels, catData);
        drawBar(inc, exp);
    });
}

function drawDonut(labels, values) {
    const ctx = document.getElementById('donutChart').getContext('2d');
    if (donutChart) donutChart.destroy();
    donutChart = new Chart(ctx, {
        type: 'doughnut',
        data: { labels: labels, datasets: [{ data: values, backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff'], borderWidth: 1 }] },
        options: { plugins: { tooltip: { callbacks: { label: (c) => ` ${c.label}: ${c.raw} ${symbols[currentCurrency]}` } }, legend: { position: 'bottom' } } }
    });
}

function drawBar(inc, exp) {
    const ctx = document.getElementById('barChart').getContext('2d');
    if (barChart) barChart.destroy();
    barChart = new Chart(ctx, {
        type: 'bar',
        data: { labels: ['Доходи', 'Витрати'], datasets: [{ label: symbols[currentCurrency], data: [inc, exp], backgroundColor: ['#198754', '#dc3545'], borderRadius: 5 }] },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
}

// --- Форми ---
document.getElementById('createWalletForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fetch('api.php?action=add_wallet', { method: 'POST', body: fd }).then(() => {
        loadDashboard();
        bootstrap.Modal.getInstance(document.getElementById('createWalletModal')).hide();
        e.target.reset();
    });
});

document.getElementById('transactionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fetch('api.php?action=add_transaction', { method: 'POST', body: fd }).then(res => res.json()).then(data => {
        if(data.success) {
            loadDashboard(); loadCharts(); e.target.reset();
            document.getElementById('date').valueAsDate = new Date();
            document.getElementById('typeExp').checked = true;
        } else { alert(data.error); }
    });
});

// --- Редагування ---
function openEditModal(wallet) {
    document.getElementById('edit_w_id').value = wallet.id;
    document.getElementById('edit_w_name').value = wallet.name;
    document.getElementById('edit_w_balance').value = wallet.balance;
    document.getElementById('edit_w_color').value = wallet.color;
    editModalInstance = new bootstrap.Modal(document.getElementById('editWalletModal'));
    editModalInstance.show();
}

document.getElementById('editWalletForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fetch('api.php?action=update_wallet', { method: 'POST', body: fd }).then(() => {
        loadDashboard(); editModalInstance.hide();
    });
});

function deleteWallet() {
    if(!confirm('Видалити?')) return;
    const fd = new FormData();
    fd.append('id', document.getElementById('edit_w_id').value);
    fetch('api.php?action=delete_wallet', { method: 'POST', body: fd }).then(() => {
        loadDashboard(); loadCharts(); editModalInstance.hide();
    });
}

// --- ТЕМНА ТЕМА ---

// 1. Перевірка при завантаженні
document.addEventListener("DOMContentLoaded", () => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        updateThemeIcon(true);
        Chart.defaults.color = '#e4e6eb'; // Білий текст для графіків
        Chart.defaults.borderColor = '#3e4042'; // Темні лінії сітки
    } else {
        Chart.defaults.color = '#2d3436';
        Chart.defaults.borderColor = '#e1e4e8';
    }
    
    // ... ваш існуючий код ...
});

// 2. Функція перемикання (викликається кнопкою)
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    let newTheme = 'light';

    if (currentTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'light');
        localStorage.setItem('theme', 'light');
        updateThemeIcon(false);
        
        // Оновлюємо графіки для світлої теми
        Chart.defaults.color = '#2d3436';
        Chart.defaults.borderColor = '#e1e4e8';
    } else {
        newTheme = 'dark';
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
        updateThemeIcon(true);
        
        // Оновлюємо графіки для темної теми
        Chart.defaults.color = '#e4e6eb';
        Chart.defaults.borderColor = '#3e4042';
    }
    
    // Перемалювати графіки з новими кольорами
    loadCharts();
}

// 3. Зміна іконки
function updateThemeIcon(isDark) {
    const btn = document.getElementById('themeBtn');
    if (isDark) {
        btn.innerHTML = '<i class="bi bi-sun-fill text-warning"></i>'; // Сонечко
    } else {
        btn.innerHTML = '<i class="bi bi-moon-stars-fill text-dark"></i>'; // Місяць
    }
}