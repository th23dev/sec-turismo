<?php
// Conexão com o banco de forma segura (sem credenciais hardcoded).
// Lê as credenciais do .env via helper manual.

require_once __DIR__ . '/../Utils/security.php';
require_once __DIR__ . '/../Utils/env.php';
require_once __DIR__ . '/../config.php';

secure_session_start();
send_security_headers();

$user = env('DB_USER', 'root');
$password = env('DB_PASSWORD', '');
$database = env('DB_NAME', 'sec_turismo');
$host = env('DB_HOST', 'localhost');

try {
    $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Compatibilidade (caso o projeto use $mysqli)
    global $pdo, $mysqli;
    $mysqli = $pdo;
} catch (PDOException $e) {
    if (defined('DB_OPTIONAL') && DB_OPTIONAL) {
        global $pdo, $mysqli;
        $pdo = null;
        $mysqli = null;
        return;
    }

    // Evita vazar detalhes sensíveis.
    http_response_code(500);
    die('Erro na conexão com o banco de dados.');
}

