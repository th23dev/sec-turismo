<?php

// Carrega variáveis de ambiente a partir de um arquivo .env (formato KEY=VALUE).
// Implementação manual simples para evitar dependência de vendor/autoload.

function env(string $key, ?string $default = null): ?string
{
    // Já existe no PHP (Apache/FPM) => prefere.
    $fromServer = getenv($key);
    if ($fromServer !== false) {
        return $fromServer;
    }

    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $root = dirname(__DIR__, 2); // base do projeto
        $envFile = $root . '/.env';
        if (!is_readable($envFile)) {
            $_ENV = $_ENV; // noop
            $cache = [];
        } else {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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

                // Remove aspas simples/dobras
                $v = trim($v, "\"' ");

                // Remove inline comentários (#) apenas se não houver aspas.
                // (mantém simples; .env.example não usa comentários inline)
                $cache[$k] = $v;
            }
        }
    }

    return $cache[$key] ?? $default;
}

