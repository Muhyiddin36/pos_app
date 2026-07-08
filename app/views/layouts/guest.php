<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($title ?? 'Masuk') . ' - ' . APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
    <div class="guest-wrap">
        <div style="width:100%;max-width:380px;">
            <?php require VIEW_PATH . '/layouts/partials/alerts.php'; ?>
            <?php $content(); ?>
        </div>
    </div>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
