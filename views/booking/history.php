<?php $pageTitle = 'My bookings — EMT'; ?>
<h1 class="h3 mb-3">My bookings</h1>
<?php if ($m = flash('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
<div class="table-responsive card shadow-sm">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Booking code</th>
                <th>Event</th>
                <th>When</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Booked</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><code><?= e($b['booking_code']) ?></code></td>
                    <td><?= e($b['event_title']) ?></td>
                    <td><?= e(date('M j, Y H:i', strtotime((string) $b['event_date']))) ?></td>
                    <td><?= (int) $b['quantity'] ?></td>
                    <td><?= e(number_format((float) $b['total_price'], 2)) ?></td>
                    <td><?= e(date('Y-m-d H:i', strtotime((string) $b['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php if (count($bookings) === 0): ?>
    <p class="text-muted mt-3">No bookings yet. <a href="<?= e(url('event', 'index')) ?>">Browse events</a>.</p>
<?php endif; ?>