<?php $pageTitle = 'All events — Admin'; ?>
<h1 class="h3 mb-3">All events</h1>
<?php if ($m = flash('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
<div class="table-responsive card shadow-sm">
    <table class="table table-striped mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Organiser</th>
                <th>Date</th>
                <th>Seats left</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $ev): ?>
                <tr>
                    <td><?= (int) $ev['id'] ?></td>
                    <td><?= e($ev['title']) ?></td>
                    <td><?= e($ev['organiser_name']) ?></td>
                    <td><?= e(date('Y-m-d H:i', strtotime((string) $ev['event_date']))) ?></td>
                    <td><?= (int) $ev['available_seats'] ?> / <?= (int) $ev['total_seats'] ?></td>
                    <td>
                        <form method="post" action="<?= e(url('admin', 'deleteevent')) ?>" class="d-inline" onsubmit="return confirm('Delete this event?');">
                            <input type="hidden" name="id" value="<?= (int) $ev['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
