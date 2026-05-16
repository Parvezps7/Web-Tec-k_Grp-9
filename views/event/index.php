<?php
$pageTitle = 'Events — EMT';
?>
<h1 class="h3 mb-3">Events</h1>
<?php if ($m = flash('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
<?php if ($m = flash('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
<form class="row g-2 mb-4" method="get" action="<?= e(url('event', 'index')) ?>">
    <input type="hidden" name="c" value="event">
    <input type="hidden" name="a" value="index">
    <div class="col-sm-8 col-md-6">
        <input class="form-control" type="search" name="q" placeholder="Search title, location, description" value="<?= e($q) ?>">
    </div>
    <div class="col-auto">
        <button class="btn btn-primary" type="submit">Search</button>
    </div>
</form>
<div class="row g-3">
    <?php foreach ($events as $ev): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <?php if (!empty($ev['image'])): ?>
                    <img src="<?= e(BASE_URL . '/uploads/' . $ev['image']) ?>" class="card-img-top object-fit-cover" style="height:180px" alt="">
                <?php else: ?>
                    <div class="bg-body-secondary text-center py-5 text-muted small">No image</div>
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <h2 class="h6 card-title"><?= e($ev['title']) ?></h2>
                    <p class="small text-muted mb-1"><?= e($ev['category_name']) ?></p>
                    <p class="small mb-1"><?= e(date('M j, Y g:i A', strtotime((string) $ev['event_date']))) ?></p>
                    <p class="small mb-2"><?= e($ev['location']) ?></p>
                    <p class="small mb-2">From <strong><?= e(number_format((float) $ev['ticket_price'], 2)) ?></strong> · <?= (int) $ev['available_seats'] ?> seats left</p>
                    <a class="btn btn-sm btn-primary mt-auto" href="<?= e(url('event', 'detail', ['id' => (int) $ev['id']])) ?>">View &amp; book</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php if (count($events) === 0): ?>
    <p class="text-muted mt-3">No events match your search.</p>
<?php endif; ?>