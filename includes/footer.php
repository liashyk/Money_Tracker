</div> <div class="modal fade" id="createWalletModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Новий гаманець</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="createWalletForm">
                    <input type="text" name="name" id="new_w_name" class="form-control mb-3" placeholder="Назва" required>
                    <input type="number" name="balance" id="new_w_balance" class="form-control mb-3" placeholder="Баланс (UAH)" value="0">
                    <input type="color" name="color" id="new_w_color" class="form-control form-control-color w-100 mb-3" value="#0d6efd">
                    <button type="submit" class="btn btn-primary w-100">Створити</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editWalletModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Редагування</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="editWalletForm">
                    <input type="hidden" name="id" id="edit_w_id">
                    <label>Назва</label><input type="text" name="name" id="edit_w_name" class="form-control mb-2" required>
                    <label>Баланс (UAH)</label><input type="number" name="balance" id="edit_w_balance" class="form-control mb-2" step="0.01" required>
                    <label>Колір</label><input type="color" name="color" id="edit_w_color" class="form-control form-control-color w-100 mb-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success flex-grow-1">Зберегти</button>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteWallet()">Видалити</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/app.js"></script> </body>
</html>