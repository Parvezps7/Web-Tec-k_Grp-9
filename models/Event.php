<?php

declare(strict_types=1);

class Event
{
    public static function allPublic(mysqli $db, ?string $search = null): array
    {
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
}