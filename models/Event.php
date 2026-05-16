<?php

declare(strict_types=1);

class Event
{
    public static function allPublic(mysqli $db, ?string $search = null): array
    {
        if ($search !== null && $search !== '') {
            $like = '%' . $search . '%';
            $sql = 'SELECT e.*, c.name AS category_name, u.name AS organiser_name
                    FROM events e
                    JOIN categories c ON c.id = e.category_id
                    JOIN users u ON u.id = e.organiser_id
                    WHERE e.title LIKE ? OR e.location LIKE ? OR e.description LIKE ?
                    ORDER BY e.event_date ASC';
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param('sss', $like, $like, $like);
            $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $rows;
        }

        $sql = 'SELECT e.*, c.name AS category_name, u.name AS organiser_name
                FROM events e
                JOIN categories c ON c.id = e.category_id
                JOIN users u ON u.id = e.organiser_id
                ORDER BY e.event_date ASC';
        $stmt = $db->prepare($sql);
        if (!$stmt || !$stmt->execute()) {
            return [];
        }
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public static function allForAdmin(mysqli $db): array
    {
        $sql = 'SELECT e.*, c.name AS category_name, u.name AS organiser_name
                FROM events e
                JOIN categories c ON c.id = e.category_id
                JOIN users u ON u.id = e.organiser_id
                ORDER BY e.id DESC';
        $stmt = $db->prepare($sql);
        if (!$stmt || !$stmt->execute()) {
            return [];
        }
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public static function forOrganiser(mysqli $db, int $organiserId): array
    {
        $sql = 'SELECT e.*, c.name AS category_name
                FROM events e
                JOIN categories c ON c.id = e.category_id
                WHERE e.organiser_id = ?
                ORDER BY e.event_date DESC';
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $organiserId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public static function find(mysqli $db, int $id): ?array
    {
        $sql = 'SELECT e.*, c.name AS category_name, u.name AS organiser_name
                FROM events e
                JOIN categories c ON c.id = e.category_id
                JOIN users u ON u.id = e.organiser_id
                WHERE e.id = ? LIMIT 1';
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public static function create(
        mysqli $db,
        int $organiserId,
        int $categoryId,
        string $title,
        string $description,
        string $eventDate,
        string $location,
        float $ticketPrice,
        int $totalSeats,
        ?string $imagePath
    ): bool {
        $available = $totalSeats;
        $sql = 'INSERT INTO events (organiser_id, category_id, title, description, event_date, location, ticket_price, total_seats, available_seats, image)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        // Types: organiser(i), category(i), title(s), description(s), date(s), location(s), price(d), total(i), available(i), image(s)
        $stmt->bind_param(
            'iissssdiis',
            $organiserId,
            $categoryId,
            $title,
            $description,
            $eventDate,
            $location,
            $ticketPrice,
            $totalSeats,
            $available,
            $imagePath
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function update(
        mysqli $db,
        int $id,
        int $organiserId,
        int $categoryId,
        string $title,
        string $description,
        string $eventDate,
        string $location,
        float $ticketPrice,
        int $totalSeats,
        ?string $imagePath,
        bool $replaceImage
    ): bool {
        $existing = self::find($db, $id);
        if (!$existing || (int) $existing['organiser_id'] !== $organiserId) {
            return false;
        }

        $sold = (int) $existing['total_seats'] - (int) $existing['available_seats'];
        $newAvailable = max(0, $totalSeats - $sold);

        if ($replaceImage && $imagePath !== null) {
            $sql = 'UPDATE events SET category_id = ?, title = ?, description = ?, event_date = ?, location = ?, ticket_price = ?, total_seats = ?, available_seats = ?, image = ?
                    WHERE id = ? AND organiser_id = ?';
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                return false;
            }
            // Types: category(i), 4×string, price(d), total(i), available(i), image(s), id(i), organiser(i)
            $stmt->bind_param(
                'issssdiisii',
                $categoryId,
                $title,
                $description,
                $eventDate,
                $location,
                $ticketPrice,
                $totalSeats,
                $newAvailable,
                $imagePath,
                $id,
                $organiserId
            );
        } else {
            $sql = 'UPDATE events SET category_id = ?, title = ?, description = ?, event_date = ?, location = ?, ticket_price = ?, total_seats = ?, available_seats = ?
                    WHERE id = ? AND organiser_id = ?';
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param(
                'issssdiiii',
                $categoryId,
                $title,
                $description,
                $eventDate,
                $location,
                $ticketPrice,
                $totalSeats,
                $newAvailable,
                $id,
                $organiserId
            );
        }
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function deleteByOrganiser(mysqli $db, int $id, int $organiserId): bool
    {
        $sql = 'DELETE FROM events WHERE id = ? AND organiser_id = ?';
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('ii', $id, $organiserId);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function deleteById(mysqli $db, int $id): bool
    {
        $sql = 'DELETE FROM events WHERE id = ?';
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}