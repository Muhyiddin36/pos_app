<div class="content-header">
    <div>
        <h1>Selamat datang, <?= e(Auth::user()['full_name']) ?></h1>
        <p class="text-muted mb-0">Ringkasan operasional hari ini, <?= e(date('d F Y')) ?></p>
    </div>
</div>

<div class="grid grid-cols-4">
    <div class="card stat-card">
        <div class="stat-icon blue">&#128722;</div>
        <div class="stat-label">Penjualan Hari Ini</div>
        <div class="stat-value"><?= (int) $salesToday['trx_count'] ?> trx</div>
        <div class="text-muted"><?= rupiah($salesToday['total_omzet']) ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon green">&#128241;</div>
        <div class="stat-label">Pulsa & Data Hari Ini</div>
        <div class="stat-value"><?= (int) $pulsaToday['trx_count'] ?> trx</div>
        <div class="text-muted"><?= rupiah($pulsaToday['total_omzet']) ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon amber">&#128274;</div>
        <div class="stat-label">Gadai Aktif</div>
        <div class="stat-value"><?= (int) $pawnActive ?></div>
        <div class="text-muted">Outstanding <?= rupiah($pawnOutstanding) ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon red">&#128176;</div>
        <div class="stat-label">Pinjaman Aktif</div>
        <div class="stat-value"><?= (int) $loanActive ?></div>
        <div class="text-muted">Outstanding <?= rupiah($loanOutstanding) ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon blue">&#127974;</div>
        <div class="stat-label">Transfer/Setor Hari Ini</div>
        <div class="stat-value"><?= (int) $bankToday['trx_count'] ?> trx</div>
        <div class="text-muted"><?= rupiah($bankToday['total_amount']) ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon amber">&#128295;</div>
        <div class="stat-label">Servis HP Berjalan</div>
        <div class="stat-value"><?= (int) $serviceOpen ?></div>
        <div class="text-muted"><?= (int) $serviceReady ?> siap diambil</div>
    </div>
</div>

<?php if ($canSeeProfit): ?>
<div class="grid grid-cols-2">
    <div class="card">
        <div class="card-header"><h3>Laba Hari Ini</h3></div>
        <div class="card-body">
            <div class="pos-summary-row"><span>Laba Penjualan Aksesoris</span><strong class="text-success"><?= rupiah($salesToday['total_profit']) ?></strong></div>
            <div class="pos-summary-row"><span>Laba Pulsa, Data & Top Up Saldo</span><strong class="text-success"><?= rupiah($pulsaToday['total_profit']) ?></strong></div>
            <div class="pos-summary-row"><span>Laba Transfer/Setor Bank</span><strong class="text-success"><?= rupiah($bankToday['total_profit']) ?></strong></div>
            <div class="pos-summary-row total"><span>Total Laba</span><strong class="text-success"><?= rupiah($salesToday['total_profit'] + $pulsaToday['total_profit'] + $bankToday['total_profit']) ?></strong></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Stok Menipis</h3></div>
        <div class="card-body" style="padding:0;">
            <?php if (empty($lowStock)): ?>
                <p class="empty-state">Semua stok produk dalam kondisi aman.</p>
            <?php else: ?>
                <div class="table-wrap">
                <table>
                    <thead><tr><th>Produk</th><?php if (Auth::isSuperAdmin()): ?><th>Cabang</th><?php endif; ?><th class="text-right">Stok</th><th class="text-right">Min</th></tr></thead>
                    <tbody>
                    <?php foreach ($lowStock as $p): ?>
                        <tr>
                            <td><?= e($p['name']) ?></td>
                            <?php if (Auth::isSuperAdmin()): ?><td><?= e($p['branch_name'] ?? '-') ?></td><?php endif; ?>
                            <td class="text-right"><span class="badge badge-danger"><?= (int) $p['stock_qty'] ?></span></td>
                            <td class="text-right"><?= (int) $p['min_stock'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><h3>Akses Cepat</h3></div>
    <div class="card-body grid grid-cols-4">
        <?php if (Auth::can('sales.create')): ?><a class="btn btn-outline" href="<?= e(url('sales/pos')) ?>">Kasir Aksesoris</a><?php endif; ?>
        <?php if (Auth::can('pulsa.create')): ?><a class="btn btn-outline" href="<?= e(url('pulsa/index')) ?>">Transaksi Pulsa</a><?php endif; ?>
        <?php if (Auth::can('pawn.create')): ?><a class="btn btn-outline" href="<?= e(url('pawn/create')) ?>">Gadai Baru</a><?php endif; ?>
        <?php if (Auth::can('loan.create')): ?><a class="btn btn-outline" href="<?= e(url('loan/create')) ?>">Pinjaman Baru</a><?php endif; ?>
        <?php if (Auth::can('bank.create')): ?><a class="btn btn-outline" href="<?= e(url('bank/index')) ?>">Transfer/Setor Bank</a><?php endif; ?>
        <?php if (Auth::can('service.create')): ?><a class="btn btn-outline" href="<?= e(url('service/create')) ?>">Terima Servis HP</a><?php endif; ?>
    </div>
</div>
