<?php $currentUser = Auth::user(); ?>
<header class="topbar">
    <div class="flex gap-md" style="align-items:center;">
        <button class="menu-toggle" data-sidebar-toggle aria-label="Buka menu">&#9776;</button>
        <div>
            <div class="topbar-title"><?= e($title ?? 'Dashboard') ?></div>
        </div>
    </div>
    <div class="topbar-user">
        <span class="badge badge-info badge-role"><?= e($currentUser['role_name'] ?? '') ?></span>
        <?php if (!empty($currentUser['branch_id'])):
            $branch = BranchModel::find((int) $currentUser['branch_id']); ?>
            <span class="badge badge-gray"><?= e($branch['name'] ?? '-') ?></span>
        <?php else: ?>
            <span class="badge badge-gray">Semua Cabang</span>
        <?php endif; ?>

        <div class="user-menu">
            <button type="button" class="user-menu-btn">
                <span class="avatar"><?= e(strtoupper(substr($currentUser['full_name'] ?? '?', 0, 1))) ?></span>
                <span><?= e($currentUser['full_name'] ?? '') ?></span>
            </button>
            <div class="user-menu-dropdown">
                <a href="<?= e(url('settings/profile')) ?>">Profil Saya</a>
                <form action="<?= e(url('auth/logout')) ?>" method="post">
                    <?= Csrf::field() ?>
                    <button type="submit">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>
