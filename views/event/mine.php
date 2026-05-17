<?php $pageTitle = 'My events — EMT'; ?>
<h1 class="h3 mb-3">My events</h1>
<?php if ($m = flash('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
<?php if ($m = flash('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
<div class="d-flex gap-2 mb-3">
    <a class="btn btn-primary" href="<?= e(url('event', 'create')) ?>">New event</a>
    <a class="btn btn-outline-secondary" href="<?= e(url('event', 'index')) ?>">All public events</a>
</div>
<div class="table-responsive card shadow-sm">
    <table class="table table-striped mb-0">
        <thead>
            <tr>
                <th>Title</th>
                <th>Date</th>
                <th>Seats</th>
                <th>Price</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $ev): ?>
                <tr>
                    <td><?= e($ev['title']) ?></td>
                    <td><?= e(date('Y-m-d H:i', strtotime((string) $ev['event_date']))) ?></td>
                    <td><?= (int) $ev['available_seats'] ?> / <?= (int) $ev['total_seats'] ?></td>
                    <td><?= e(number_format((float) $ev['ticket_price'], 2)) ?></td>
                    <td class="text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="<?= e(url('event', 'edit', ['id' => (int) $ev['id']])) ?>">Edit</a>
                        <form class="d-inline" method="post" action="<?= e(url('event', 'delete')) ?>" onsubmit="return confirm('Delete this event?');">
                            <input type="hidden" name="id" value="<?= (int) $ev['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php if (count($events) === 0): ?>
    <p class="text-muted mt-3">You have no events yet.</p>
<?php endif; ?>
