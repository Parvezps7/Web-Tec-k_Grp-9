<?php

declare(strict_types=1);

/**
 * Single shared mysqli connection (UTF-8).
 */
function connect_db(): mysqli
{
    static $mysqli = null;
    if ($mysqli instanceof mysqli) {
        return $mysqli;
    }

    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $name = 'emt_db';

    $mysqli = new mysqli($host, $user, $pass, $name);
    if ($mysqli->connect_errno) {
        exit('Database connection failed. Import database/schema.sql in phpMyAdmin.');
    }
    $mysqli->set_charset('utf8mb4');

    return $mysqli;
}
