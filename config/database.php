<?php
// config/database.php

$host = '127.0.0.9'; // Assuming XAMPP uses localhost or 127.0.0.1. Using 127.0.0.1 is safer for PDO in some cases. Given user asked for XAMPP.
$host = '127.0.0.1';
$db   = 'bloodnet_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In production, log this error instead of showing it to the user.
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
