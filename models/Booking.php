<?php

declare(strict_types=1);

class Booking
{
    public static function forAttendee(mysqli $db, int $attendeeId): array
    {
        $sql = 'SELECT b.*, e.title AS event_title, e.event_date, e.location
                FROM bookings b
                JOIN events e ON e.id = b.event_id
                WHERE b.attendee_id = ?
                ORDER BY b.created_at DESC';
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $attendeeId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public static function createWithSeatUpdate(
        mysqli $db,
        int $attendeeId,
        int $eventId,
        int $quantity,
        float $unitPrice
    ): ?string {
        $db->begin_transaction();
        try {
            $sqlLock = 'SELECT id, available_seats, ticket_price FROM events WHERE id = ? FOR UPDATE';
            $stmt = $db->prepare($sqlLock);
            if (!$stmt) {
                throw new RuntimeException('prepare failed');
            }
            $stmt->bind_param('i', $eventId);
            $stmt->execute();
            $res = $stmt->get_result();
            $event = $res->fetch_assoc();
            $stmt->close();
            if (!$event) {
                $db->rollback();
                return null;
            }
            $available = (int) $event['available_seats'];
            $price = (float) $event['ticket_price'];
            if ($quantity < 1 || $available < $quantity) {
                $db->rollback();
                return null;
            }
            if (abs($price - $unitPrice) > 0.009) {
                $db->rollback();
                return null;
            }

            $total = round($price * $quantity, 2);
            $code = bin2hex(random_bytes(8));

            $sqlIns = 'INSERT INTO bookings (attendee_id, event_id, quantity, total_price, booking_code) VALUES (?, ?, ?, ?, ?)';
            $stmt = $db->prepare($sqlIns);
            if (!$stmt) {
                throw new RuntimeException('prepare failed');
            }
            $stmt->bind_param('iiids', $attendeeId, $eventId, $quantity, $total, $code);
            if (!$stmt->execute()) {
                $stmt->close();
                $db->rollback();
                return null;
            }
            $stmt->close();

            $newAvail = $available - $quantity;
            $sqlUp = 'UPDATE events SET available_seats = ? WHERE id = ?';
            $stmt = $db->prepare($sqlUp);
            if (!$stmt) {
                throw new RuntimeException('prepare failed');
            }
            $stmt->bind_param('ii', $newAvail, $eventId);
            if (!$stmt->execute()) {
                $stmt->close();
                $db->rollback();
                return null;
            }
            $stmt->close();

            $db->commit();
            return $code;
        } catch (Throwable $e) {
            $db->rollback();
            return null;
        }
    }
}