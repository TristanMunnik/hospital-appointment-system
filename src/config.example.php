<?php
// Copy this file to config.php and fill in your own database credentials.
// config.php is git-ignored so real credentials never reach the repository.
$host = 'localhost';
$db   = 'hospital_system';
$user = 'your_db_user';
$pass = 'your_db_password';
$chrs = 'utf8mb4';

$attr = "mysql:host=$host;dbname=$db;charset=$chrs";

$opts = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($attr, $user, $pass, $opts);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    throw new RuntimeException('Unable to connect to the hospital database.', 0, $e);
}
