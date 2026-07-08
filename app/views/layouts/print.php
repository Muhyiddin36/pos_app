<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Cetak') ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body onload="window.print()">
    <?php $content(); ?>
    <div class="no-print" style="text-align:center;margin-top:1rem;">
        <button class="btn" onclick="window.print()">Cetak Ulang</button>
        <button class="btn btn-secondary" onclick="window.close()">Tutup</button>
    </div>
</body>
</html>
