<?php
// Centraliza constantes de caminho/URL usadas pelo projeto.
// Ajuste BASE_URL no .env (quando necessário).

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__)); // diretório raiz do projeto (sec-turismo)
}

// BASE_URL é opcional; útil para gerar URLs absolutas/consistentes.
$baseUrl = '';
$envPath = BASE_PATH . '/.env';
if (is_readable($envPath)) {
    // Leitura simples, sem dependência.
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }
        [$k, $v] = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v);
        $v = trim($v, "\"' ");
        if ($k === 'BASE_URL') {
            $baseUrl = $v;
            break;
        }
    }
}

if (!defined('BASE_URL')) {
    define('BASE_URL', $baseUrl);
}

