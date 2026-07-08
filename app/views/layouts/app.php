<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($title ?? 'Dashboard') . ' - ' . APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
    <script>window.BASE_URL = <?= json_encode(BASE_URL) ?>;</script>
</head>
<body>
<div class="app-shell">
    <?php require VIEW_PATH . '/layouts/partials/sidebar.php'; ?>
    <div class="main-wrap">
        <?php require VIEW_PATH . '/layouts/partials/header.php'; ?>
        <main class="content">
            <?php require VIEW_PATH . '/layouts/partials/alerts.php'; ?>
            <?php $content(); ?>
        </main>
    </div>
</div>
<script src="<?= e(asset('js/app.js')) ?>"></script>
<?php if (!empty($extraScripts)) : foreach ($extraScripts as $script): ?>
    <script src="<?= e(asset($script)) ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
