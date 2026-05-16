<?php
$pageTitle = $event['title'] . ' — EMT';
$user = current_user();
$canBook = $user && in_array($user['role'], ['attendee', 'organiser'], true);
?>
<?php if ($m = flash('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
<?php if ($m = flash('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
<h1 class="h3 mb-2"><?= e($event['title']) ?></h1>
<p class="text-muted"><?= e($event['category_name']) ?> · <?= e($event['organiser_name']) ?></p>
<div class="row g-4">
    <div class="col-lg-7">
        <?php if (!empty($event['image'])): ?>
            <img src="<?= e(BASE_URL . '/uploads/' . $event['image']) ?>" class="img-fluid rounded shadow-sm w-100 object-fit-cover" style="max-height:360px" alt="">
        <?php endif; ?>
        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <h2 class="h6">Description</h2>
                <p class="mb-0"><?= nl2br(e((string) $event['description'])) ?></p>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <p><strong>When:</strong> <?= e(date('l, F j, Y g:i A', strtotime((string) $event['event_date']))) ?></p>
                <p><strong>Where:</strong> <?= e($event['location']) ?></p>
                <p><strong>Price per ticket:</strong> <?= e(number_format((float) $event['ticket_price'], 2)) ?></p>
                <p class="mb-1"><strong>Seats available:</strong> <span id="avail-display"><?= (int) $event['available_seats'] ?></span></p>
                <?php if ($canBook): ?>
                    <hr>
                    <h2 class="h6">Book tickets</h2>
                    <form method="post" action="<?= e(url('booking', 'store')) ?>" id="booking-form">
                        <input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>">
                        <input type="hidden" name="ticket_price" value="<?= e((string) (float) $event['ticket_price']) ?>">
                        <div class="mb-3">
                            <label class="form-label" for="quantity">Quantity</label>
                            <input class="form-control" type="number" name="quantity" id="quantity" min="1" max="<?= (int) $event['available_seats'] ?>" value="1" required
                                   data-event-id="<?= (int) $event['id'] ?>">
                        </div>
                        <p class="small text-muted" id="seat-feedback" aria-live="polite">Select quantity to check availability.</p>
                        <button class="btn btn-success" type="submit" id="book-btn">Confirm booking</button>
                    </form>
                <?php elseif (!$user): ?>
                    <p class="small text-muted mb-0"><a href="<?= e(url('auth', 'login')) ?>">Log in</a> as attendee or organiser to book.</p>
                <?php else: ?>
                    <p class="small text-muted mb-0">Admin accounts cannot book from this demo.</p>
                <?php endif; ?>
            </div>
        </div>
        <a class="btn btn-outline-secondary mt-3" href="<?= e(url('event', 'index')) ?>">Back to events</a>
    </div>
</div>
<?php if ($canBook): ?>
<script src="<?= e(BASE_URL) ?>/assets/js/seat-check.js" defer></script>
<?php endif; ?>