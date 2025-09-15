<?php
// db-status.php

$host = 'localhost';
$db   = 'u952857351_sizerdb';
$user = 'u952857351_sizerdbadmin';
$pass = 'dKwIvuTO';
$status = '';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $status = '<span style="color: green;">Database connected successfully.</span>';
} catch (PDOException $e) {
    $status = '<span style="color: red;">Database connection failed: '
        . htmlspecialchars($e->getMessage()) . '</span>';
}

echo $status;