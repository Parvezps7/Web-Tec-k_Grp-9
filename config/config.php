<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', dirname(__DIR__));

$docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
$projRoot = realpath(dirname(__DIR__)) ?: '';
$webBase = '';
if ($docRoot !== '' && $projRoot !== '' && str_starts_with($projRoot, $docRoot)) {
    $webBase = substr($projRoot, strlen($docRoot));
    $webBase = str_replace('\\', '/', $webBase);
    $webBase = '/' . ltrim($webBase, '/');
    if ($webBase === '/') {
        $webBase = '';
    } else {
        $webBase = rtrim($webBase, '/');
    }
} else {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if (str_ends_with($scriptDir, '/ajax')) {
        $scriptDir = dirname($scriptDir);
    }
    $webBase = $scriptDir === '/' || $scriptDir === '' ? '' : rtrim($scriptDir, '/');
}
define('BASE_URL', $webBase);

define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

require_once ROOT_PATH . '/config/helpers.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/config/BaseController.php';

spl_autoload_register(function (string $class): void {
    $paths = [
        ROOT_PATH . '/models/' . $class . '.php',
        ROOT_PATH . '/controllers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});
