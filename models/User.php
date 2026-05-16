<?php

declare(strict_types=1);

class User
{
    public static function findByEmail(mysqli $db, string $email): ?array
    {
        $sql = 'SELECT id, name, email, password, role, created_at FROM users WHERE email = ? LIMIT 1';
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public static function findById(mysqli $db, int $id): ?array
    {
        $sql = 'SELECT id, name, email, role, created_at FROM users WHERE id = ? LIMIT 1';
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

    public static function create(mysqli $db, string $name, string $email, string $passwordHash, string $role): bool
    {
        $sql = 'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('ssss', $name, $email, $passwordHash, $role);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function all(mysqli $db): array
    {
        $sql = 'SELECT id, name, email, role, created_at FROM users ORDER BY id ASC';
        $stmt = $db->prepare($sql);
        if (!$stmt || !$stmt->execute()) {
            return [];
        }
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public static function delete(mysqli $db, int $id): bool
    {
        $sql = 'DELETE FROM users WHERE id = ?';
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
