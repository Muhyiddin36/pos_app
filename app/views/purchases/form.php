<div class="content-header">
    <h1>Tambah Pembelian</h1>
</div>

<form method="post" action="<?= e(url('purchases/store')) ?>" id="purchase-form">
    <?= Csrf::field() ?>
    <div class="card">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>No. Faktur</label>
                    <input type="text" name="invoice_no" placeholder="Otomatis jika dikosongkan">
                </div>
                <div class="form-group">
                    <label>Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" value="<?= e(date('Y-m-d')) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Supplier</label>
                    <select name="supplier_id">
                        <option value="">-- Tanpa Supplier --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= (int) $s['id'] ?>"><?= e($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Catatan</label>
                    <input type="text" name="note">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Item Pembelian</h3>
            <button type="button" class="btn btn-sm" id="btn-add-row">+ Tambah Baris</button>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Produk</th><th style="width:110px;">Qty</th><th style="width:160px;">Harga Beli</th><th style="width:150px;">Subtotal</th><th></th></tr></thead>
                <tbody id="item-rows"></tbody>
            </table>
        </div>
        <div class="card-footer flex-between">
            <strong>Total: <span id="grand-total">Rp 0</span></strong>
            <div class="flex gap-sm">
                <button type="submit" class="btn">Simpan Pembelian</button>
                <a class="btn btn-secondary" href="<?= e(url('purchases/index')) ?>">Batal</a>
            </div>
        </div>
    </div>
</form>

<script id="product-data" type="application/json"><?= json_encode(array_map(fn ($p) => ['id' => (int) $p['id'], 'name' => $p['name'], 'unit' => $p['unit'], 'price' => (float) $p['purchase_price']], $products), JSON_UNESCAPED_UNICODE) ?></script>

<script>
(function () {
    var products = JSON.parse(document.getElementById('product-data').textContent || '[]');
    var tbody = document.getElementById('item-rows');
    var grandTotalEl = document.getElementById('grand-total');

    function productOptions(selected) {
        return '<option value="">-- Pilih Produk --</option>' + products.map(function (p) {
            return '<option value="' + p.id + '" data-price="' + p.price + '" ' + (selected == p.id ? 'selected' : '') + '>' + p.name + '</option>';
        }).join('');
    }

    function addRow() {
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td><select name="product_id[]" class="prod-select" required>' + productOptions('') + '</select></td>' +
            '<td><input type="number" name="qty[]" class="qty-input" min="1" value="1" required></td>' +
            '<td><input type="number" name="price[]" class="price-input" min="0" step="0.01" value="0" required></td>' +
            '<td class="row-subtotal">Rp 0</td>' +
            '<td><button type="button" class="btn btn-sm btn-danger btn-icon btn-del-row">&times;</button></td>';
        tbody.appendChild(tr);

        var select = tr.querySelector('.prod-select');
        var priceInput = tr.querySelector('.price-input');
        var qtyInput = tr.querySelector('.qty-input');

        select.addEventListener('change', function () {
            var opt = select.options[select.selectedIndex];
            priceInput.value = opt.getAttribute('data-price') || 0;
            recalc();
        });
        priceInput.addEventListener('input', recalc);
        qtyInput.addEventListener('input', recalc);
        tr.querySelector('.btn-del-row').addEventListener('click', function () {
            tr.remove();
            recalc();
        });
        recalc();
    }

    function recalc() {
        var grand = 0;
        tbody.querySelectorAll('tr').forEach(function (tr) {
            var qty = parseFloat(tr.querySelector('.qty-input').value || '0');
            var price = parseFloat(tr.querySelector('.price-input').value || '0');
            var subtotal = qty * price;
            tr.querySelector('.row-subtotal').textContent = window.POS.formatRupiah(subtotal);
            grand += subtotal;
        });
        grandTotalEl.textContent = window.POS.formatRupiah(grand);
    }

    document.getElementById('btn-add-row').addEventListener('click', addRow);
    document.getElementById('purchase-form').addEventListener('submit', function (e) {
        if (tbody.querySelectorAll('tr').length === 0) {
            e.preventDefault();
            alert('Tambahkan minimal satu item pembelian.');
        }
    });

    addRow();
})();
</script>
