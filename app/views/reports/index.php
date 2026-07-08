<div class="content-header">
    <h1>Laporan</h1>
</div>

<div class="grid grid-cols-3">
    <a class="card" style="padding:1.25rem;display:block;" href="<?= e(url('reports/sales')) ?>">
        <h3>Laporan Penjualan</h3>
        <p class="text-muted mb-0">Rekap transaksi penjualan aksesoris HP.</p>
    </a>
    <?php if (Auth::can('reports.profit')): ?>
    <a class="card" style="padding:1.25rem;display:block;" href="<?= e(url('reports/profit')) ?>">
        <h3>Laporan Laba Rugi</h3>
        <p class="text-muted mb-0">Analisis laba penjualan aksesoris dan pulsa/data.</p>
    </a>
    <?php endif; ?>
    <a class="card" style="padding:1.25rem;display:block;" href="<?= e(url('reports/stock')) ?>">
        <h3>Laporan Stok</h3>
        <p class="text-muted mb-0">Posisi stok produk per cabang.</p>
    </a>
    <a class="card" style="padding:1.25rem;display:block;" href="<?= e(url('reports/pawn')) ?>">
        <h3>Laporan Gadai</h3>
        <p class="text-muted mb-0">Rekap transaksi gadai barang & outstanding.</p>
    </a>
    <a class="card" style="padding:1.25rem;display:block;" href="<?= e(url('reports/loan')) ?>">
        <h3>Laporan Pinjaman</h3>
        <p class="text-muted mb-0">Rekap pinjaman uang & sisa tagihan.</p>
    </a>
    <a class="card" style="padding:1.25rem;display:block;" href="<?= e(url('reports/pulsa')) ?>">
        <h3>Laporan Pulsa & Data</h3>
        <p class="text-muted mb-0">Rekap transaksi pulsa dan paket data.</p>
    </a>
</div>
