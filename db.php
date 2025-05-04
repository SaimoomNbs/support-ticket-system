<?php
$host = 'localhost';
$dbname = 'ticket';
$user = 'root';
$pass = '';

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}
?>