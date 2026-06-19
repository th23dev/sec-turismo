<?php

require_once __DIR__ . '/security.php';

secure_session_start();

if (!function_exists('csrf_token')) {

    function csrf_token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    function csrf_validate(?string $token): bool
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        // Mitiga timing attack
        return hash_equals((string)$_SESSION['csrf_token'], (string)$token);
    }
}
