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

    
}
