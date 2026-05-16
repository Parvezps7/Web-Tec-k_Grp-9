<?php

declare(strict_types=1);

class Category
{
    public static function all(mysqli $db): array
    {
        $sql = 'SELECT id, name FROM categories ORDER BY name ASC';
        $stmt = $db->prepare($sql);
        if (!$stmt || !$stmt->execute()) {
            return [];
        }
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

     public static function find(mysqli $db, int $id): ?array
    {
        $sql = 'SELECT id, name FROM categories WHERE id = ? LIMIT 1';
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
}
