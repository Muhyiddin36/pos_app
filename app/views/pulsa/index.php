<div class="content-header">
    <div>
        <h1>Pulsa, Data &amp; Top Up Saldo</h1>
        <p class="text-muted mb-0">Transaksi penjualan pulsa, paket data, token PLN, dan top up saldo e-wallet (Gopay, ShopeePay, OVO, DANA, dll).</p>
    </div>
    <?php if (Auth::can('pulsa.manage_product')): ?>
        <a class="btn btn-outline" href="<?= e(url('pulsa/products')) ?>">Kelola Produk</a>
    <?php endif; ?>
</div>

<?php if (Auth::can('pulsa.create')): ?>
<div class="card">
    <div class="card-header"><h3>Transaksi Baru</h3></div>
    <form method="post" action="<?= e(url('pulsa/store')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body form-row" style="grid-template-columns: 2fr 1fr 2fr 1fr;align-items:end;">
            <div class="form-group">
                <label>Produk</label>
                <select name="pulsa_product_id" required>
                    <option value="">-- Pilih Produk --</option>
                    <?php
                    $categoryLabels = ['pulsa' => 'Pulsa', 'paket_data' => 'Paket Data', 'pln' => 'Token PLN', 'ewallet' => 'Top Up E-Wallet', 'other' => 'Lainnya'];
                    $grouped = [];
                    foreach ($products as $p) {
                        $grouped[$p['category']][] = $p;
                    }
                    ?>
                    <?php foreach ($categoryLabels as $catKey => $catLabel): ?>
                        <?php if (empty($grouped[$catKey])) continue; ?>
                        <optgroup label="<?= e($catLabel) ?>">
                            <?php foreach ($grouped[$catKey] as $p): ?>
                                <option value="<?= (int) $p['id'] ?>"><?= e($p['provider'] . ' - ' . $p['name']) ?> (<?= rupiah($p['sale_price']) ?>)</option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>No. Tujuan</label>
                <input type="text" name="customer_phone" required placeholder="08xxxxxxxxxx">
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <input type="text" name="note">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-block">Proses</button>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('pulsa/index')) ?>" class="filter-bar mb-0">
            <div class="form-group"><label>Dari Tanggal</label><input type="date" name="from" value="<?= e($from) ?>"></div>
            <div class="form-group"><label>Sampai Tanggal</label><input type="date" name="to" value="<?= e($to) ?>"></div>
            <div class="form-group"><button class="btn btn-outline" type="submit">Filter</button></div>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Transaksi</th><th>Tanggal</th><th>Produk</th><th>No. Tujuan</th><th class="text-right">Harga Jual</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($transactions)): ?>
                <tr><td colspan="7" class="empty-state">Belum ada transaksi.</td></tr>
            <?php endif; ?>
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td><?= e($t['trx_no']) ?></td>
                    <td><?= e(date('d-m-Y H:i', strtotime($t['created_at']))) ?></td>
                    <td><?= e($t['provider'] . ' - ' . $t['product_name']) ?></td>
                    <td><?= e($t['customer_phone']) ?></td>
                    <td class="text-right"><?= rupiah($t['sale_price']) ?></td>
                    <td><span class="badge badge-success"><?= e($t['status']) ?></span></td>
                    <td class="text-right"><a class="btn btn-sm btn-outline" target="_blank" href="<?= e(url('pulsa/print/' . $t['id'])) ?>">Cetak</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
