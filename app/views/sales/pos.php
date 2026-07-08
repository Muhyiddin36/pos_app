<div id="pos-app" class="pos-layout">
    <div>
        <div class="card">
            <div class="card-body">
                <div class="pos-search">
                    <input type="search" id="pos-search" placeholder="Cari nama produk / SKU / scan barcode..." autofocus>
                </div>
                <div id="pos-product-grid" class="pos-product-grid"></div>
            </div>
        </div>
    </div>

    <div>
        <form id="pos-form" method="post" action="<?= e(url('sales/store')) ?>">
            <?= Csrf::field() ?>
            <input type="hidden" name="items_json" id="pos-items-json">
            <div class="card">
                <div class="card-header"><h3>Keranjang</h3></div>
                <div class="card-body" style="padding-top:0;">
                    <p id="pos-empty-cart" class="empty-state" style="display:none;">Keranjang masih kosong. Pilih produk di sebelah kiri.</p>
                    <div id="pos-cart-items" class="pos-cart-items"></div>

                    <div class="form-group" style="margin-top:1rem;">
                        <label>Pelanggan (opsional)</label>
                        <select name="customer_id">
                            <option value="">-- Pelanggan Umum --</option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?> <?= $c['phone'] ? '(' . e($c['phone']) . ')' : '' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Diskon (Rp)</label>
                            <input type="number" id="pos-discount" name="discount" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label>Metode Bayar</label>
                            <select name="payment_method">
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                                <option value="debit">Debit/Kredit</option>
                            </select>
                        </div>
                    </div>

                    <div class="pos-summary-row"><span>Subtotal</span><strong id="pos-subtotal">Rp 0</strong></div>
                    <div class="pos-summary-row total"><span>Total Bayar</span><strong id="pos-total">Rp 0</strong></div>

                    <div class="form-group">
                        <label>Jumlah Dibayar</label>
                        <input type="number" id="pos-paid" name="paid" value="0" min="0" required>
                    </div>
                    <div class="pos-summary-row"><span>Kembalian</span><strong id="pos-change">Rp 0</strong></div>
                </div>
                <div class="card-footer">
                    <button type="submit" id="pos-submit" class="btn btn-block btn-lg" disabled>Proses & Cetak Struk</button>
                </div>
            </div>
        </form>
    </div>
</div>
