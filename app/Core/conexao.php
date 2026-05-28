<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = 'root';
$password = '';
$database = 'sec_turismo';
$host = 'localhost';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    global $pdo, $mysqli;
    $mysqli = $pdo;  // Compatibility alias
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
