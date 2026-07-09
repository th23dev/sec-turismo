<?php

function secure_session_start(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? null) === '443')
    );

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    session_start();
}

function send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
}

function is_safe_http_url(?string $url): bool
{
    if ($url === null || trim($url) === '') {
        return true;
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    return in_array($scheme, ['http', 'https'], true);
}

function extract_iframe_src(string $value): string
{
    if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $value, $matches)) {
        return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    return trim($value);
}

function is_safe_google_maps_url(?string $url): bool
{
    if ($url === null || trim($url) === '') {
        return true;
    }

    $url = extract_iframe_src($url);
    if (!is_safe_http_url($url)) {
        return false;
    }

    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
    return $host === 'google.com'
        || str_ends_with($host, '.google.com')
        || $host === 'google.com.br'
        || str_ends_with($host, '.google.com.br')
        || $host === 'maps.app.goo.gl'
        || $host === 'goo.gl';
}
