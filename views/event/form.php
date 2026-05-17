<?php
$pageTitle = ($event ? 'Edit event' : 'Create event') . ' — EMT';
$isEdit = $event !== null;
?>
<h1 class="h3 mb-3"><?= $isEdit ? 'Edit event' : 'Create event' ?></h1>
<?php if ($m = flash('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" action="<?= e($isEdit ? url('event', 'update') : url('event', 'store')) ?>" class="card shadow-sm">
    <div class="card-body">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= (int) $event['id'] ?>">
        <?php endif; ?>
        <div class="mb-3">
            <label class="form-label" for="title">Title</label>
            <input class="form-control" id="title" name="title" required maxlength="200" value="<?= e($isEdit ? (string) $event['title'] : '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label" for="category_id">Category</label>
            <select class="form-select" id="category_id" name="category_id" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>" <?= $isEdit && (int) $event['category_id'] === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4"><?= e($isEdit ? (string) $event['description'] : '') ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label" for="event_date">Event date &amp; time</label>
            <input class="form-control" type="datetime-local" id="event_date" name="event_date" required
                   value="<?= $isEdit ? e(date('Y-m-d\TH:i', strtotime((string) $event['event_date']))) : '' ?>">
        </div>
        <div class="mb-3">
            <label class="form-label" for="location">Location</label>
            <input class="form-control" id="location" name="location" required maxlength="200" value="<?= e($isEdit ? (string) $event['location'] : '') ?>">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" for="ticket_price">Ticket price</label>
                <input class="form-control" type="number" step="0.01" min="0" id="ticket_price" name="ticket_price" required
                       value="<?= $isEdit ? e((string) (float) $event['ticket_price']) : '0' ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label" for="total_seats">Total seats</label>
                <input class="form-control" type="number" min="1" id="total_seats" name="total_seats" required
                       value="<?= $isEdit ? (int) $event['total_seats'] : '50' ?>">
                <?php if ($isEdit): ?>
                    <div class="form-text">Sold seats stay counted; available seats adjust automatically.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="image">Event image (optional, JPG/PNG/WEBP, max 2MB)</label>
            <input class="form-control" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
            <?php if ($isEdit && !empty($event['image'])): ?>
                <p class="small mt-2 mb-0">Current: <a href="<?= e(BASE_URL . '/uploads/' . $event['image']) ?>" target="_blank" rel="noopener">view</a></p>
            <?php endif; ?>
        </div>
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save changes' : 'Create event' ?></button>
        <a class="btn btn-outline-secondary" href="<?= e(url('event', 'mine')) ?>">Cancel</a>
    </div>
</form>
