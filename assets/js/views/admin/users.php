<?php $pageTitle = 'Users — Admin'; ?>
<h1 class="h3 mb-3">All users</h1>
<?php if ($m = flash('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
<?php if ($m = flash('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
<div class="table-responsive card shadow-sm">
    <table class="table table-striped mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php $self = current_user(); ?>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= (int) $u['id'] ?></td>
                    <td><?= e($u['name']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><span class="badge bg-secondary"><?= e($u['role']) ?></span></td>
                    <td><?= e(date('Y-m-d', strtotime((string) $u['created_at']))) ?></td>
                    <td>
                        <?php if ((int) $u['id'] !== $self['id']): ?>
                            <form method="post" action="<?= e(url('admin', 'deleteuser')) ?>" class="d-inline" onsubmit="return confirm('Delete this user?');">
                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted small">You</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
