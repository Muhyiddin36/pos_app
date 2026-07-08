<div class="content-header">
    <h1>Log Audit</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('audit-log/index')) ?>" class="filter-bar mb-0">
            <div class="form-group">
                <label>Modul</label>
                <input type="text" name="module" value="<?= e($filters['module']) ?>" placeholder="mis. sales, users">
            </div>
            <?php if (Auth::isSuperAdmin()): ?>
            <div class="form-group">
                <label>Cabang</label>
                <select name="branch_id">
                    <option value="">Semua Cabang</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= (int) $b['id'] ?>" <?= $filters['branch_id'] == $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="form-group"><label>Dari Tanggal</label><input type="date" name="from" value="<?= e($filters['from']) ?>"></div>
            <div class="form-group"><label>Sampai Tanggal</label><input type="date" name="to" value="<?= e($filters['to']) ?>"></div>
            <div class="form-group"><button class="btn btn-outline" type="submit">Filter</button></div>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Waktu</th><th>Pengguna</th><th>Cabang</th><th>Modul</th><th>Aksi</th><th>Keterangan</th><th>IP</th></tr></thead>
            <tbody>
            <?php if (empty($logs)): ?>
                <tr><td colspan="7" class="empty-state">Tidak ada log.</td></tr>
            <?php endif; ?>
            <?php foreach ($logs as $l): ?>
                <tr>
                    <td><?= e(date('d-m-Y H:i:s', strtotime($l['created_at']))) ?></td>
                    <td><?= e($l['user_name'] ?? 'System') ?></td>
                    <td><?= e($l['branch_name'] ?? '-') ?></td>
                    <td><span class="badge badge-gray"><?= e($l['module']) ?></span></td>
                    <td><?= e($l['action']) ?></td>
                    <td><?= e($l['description'] ?? '-') ?></td>
                    <td class="text-muted"><?= e($l['ip_address'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
