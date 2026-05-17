<?php
$pageTitle = 'Dashboard — EMT';
/** @var array $user */
?>
<h1 class="h3 mb-3">Dashboard</h1>
<?php if ($m = flash('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
<?php if ($m = flash('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <p class="mb-1"><strong><?= e($user['name']) ?></strong></p>
        <p class="text-muted small mb-0"><?= e($user['email']) ?> · Role: <span class="badge bg-secondary"><?= e($user['role']) ?></span></p>
    </div>
</div>
<div class="list-group shadow-sm">
    <a class="list-group-item list-group-item-action" href="<?= e(url('event', 'index')) ?>">Browse all events</a>
    <?php if (in_array($user['role'], ['attendee', 'organiser'], true)): ?>
        <a class="list-group-item list-group-item-action" href="<?= e(url('booking', 'history')) ?>">My bookings</a>
    <?php endif; ?>
    <?php if ($user['role'] === 'organiser'): ?>
        <a class="list-group-item list-group-item-action" href="<?= e(url('event', 'mine')) ?>">My events</a>
        <a class="list-group-item list-group-item-action" href="<?= e(url('event', 'create')) ?>">Create new event</a>
    <?php endif; ?>
    <?php if ($user['role'] === 'admin'): ?>
        <a class="list-group-item list-group-item-action" href="<?= e(url('admin', 'users')) ?>">Manage users</a>
        <a class="list-group-item list-group-item-action" href="<?= e(url('admin', 'events')) ?>">Manage all events</a>
    <?php endif; ?>
</div>
