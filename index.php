<?php require 'includes/header.php'; ?>

    <h5>Мої Гаманці <span id="walletCurrencyHint" class="text-muted fs-6">(в гривнях)</span></h5>
    <div class="row mb-3" id="walletsContainer"></div>
    
    <button class="btn btn-outline-primary mb-5" data-bs-toggle="modal" data-bs-target="#createWalletModal">
        + Додати гаманець
    </button>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm p-4 mb-4">
                <h4 class="mb-3">Нова операція</h4>
                <div class="alert alert-info py-2" style="font-size: 0.9rem;">
                    ⚠️ Вводьте суму в <b>Гривнях</b>.
                </div>
                <form id="transactionForm">
                    <div class="btn-group w-100 mb-3" role="group">
                        <input type="radio" class="btn-check" name="type" id="typeExp" value="expense" checked>
                        <label class="btn btn-outline-danger" for="typeExp">Витрата 🔴</label>
                        <input type="radio" class="btn-check" name="type" id="typeInc" value="income">
                        <label class="btn btn-outline-success" for="typeInc">Дохід 🟢</label>
                    </div>

                    <div class="mb-3">
                        <label>Гаманець</label>
                        <select id="walletSelect" name="wallet_id" class="form-select" required></select>
                    </div>

                    <div class="mb-3">
                        <label>Сума (UAH)</label>
                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" placeholder="0.00" required>
                    </div>

                    <div class="mb-3">
                        <label>Категорія</label>
                        <input type="text" name="category" id="category" class="form-control" list="catList" placeholder="Оберіть..." required>
                        <datalist id="catList">
                            <option value="Їжа"><option value="Транспорт"><option value="Розваги"><option value="Житло">
                        </datalist>
                    </div>

                    <div class="mb-3">
                        <input type="date" name="date" id="date" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">Зберегти</button>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-3 mb-4">
                <h6 class="text-center text-muted">Витрати за категоріями</h6>
                <canvas id="donutChart"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3 mb-4">
                <h6 class="text-center text-muted">Доходи vs Витрати</h6>
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

<?php require 'includes/footer.php'; ?>