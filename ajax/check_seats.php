<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/config/config.php';

$eventId = (int) ($_GET['event_id'] ?? 0);
$quantity = (int) ($_GET['quantity'] ?? 0);

if ($eventId < 1 || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$db = connect_db();
$available = Event::getAvailableSeats($db, $eventId);
if ($available === null) {
    echo json_encode(['success' => false, 'message' => 'Event not found']);
    exit;
}

$sufficient = $available >= $quantity;
$remainingAfter = max(0, $available - $quantity);

echo json_encode([
    'success' => true,
    'available' => $available,
    'quantity' => $quantity,
    'sufficient' => $sufficient,
    'remaining_after' => $remainingAfter,
]);
