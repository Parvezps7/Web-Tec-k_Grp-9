<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $controller, string $action = 'index', array $params = []): string
{
    $query = array_merge(['c' => $controller, 'a' => $action], $params);
    $base = BASE_URL === '' ? '' : BASE_URL;
    return $base . '/index.php?' . http_build_query($query);
}

function redirect(string $location): void
{
    header('Location: ' . $location);
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }
    if (!isset($_SESSION['_flash'][$key])) {
        return null;
    }
    $msg = $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);
    return $msg;
}

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        flash('error', 'Please log in to continue.');
        redirect(url('auth', 'login'));
    }
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    return [
        'id' => (int) $_SESSION['user_id'],
        'name' => (string) ($_SESSION['user_name'] ?? ''),
        'email' => (string) ($_SESSION['user_email'] ?? ''),
        'role' => (string) ($_SESSION['user_role'] ?? 'attendee'),
    ];
}

function require_role(string ...$roles): void
{
    require_login();
    $user = current_user();
    if ($user === null || !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        require ROOT_PATH . '/views/error/403.php';
        exit;
    }
}

/**
 * Validate image upload. Returns stored filename (basename) or null on failure.
 */
function save_event_upload(array $file): ?string
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        return null;
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        return null;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $map = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    if (!isset($map[$mime])) {
        return null;
    }
    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }
    $basename = bin2hex(random_bytes(8)) . '.' . $map[$mime];
    $dest = UPLOAD_PATH . '/' . $basename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }
    return $basename;
}
