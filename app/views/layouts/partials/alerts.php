<?php $flashes = Flash::all(); ?>
<?php foreach ($flashes as $type => $message): ?>
    <?php $cls = ['success' => 'alert-success', 'error' => 'alert-error', 'warning' => 'alert-warning', 'info' => 'alert-info'][$type] ?? 'alert-info'; ?>
    <div class="alert <?= $cls ?>" data-autohide><?= e($message) ?></div>
<?php endforeach; ?>
