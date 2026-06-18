<?php

function app_base_path(): string
{
    $scriptName = '/' . ltrim(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''), '/');

    $patterns = [
        '#/public/index\.php$#',
        '#/app/Views/[^/]+\.php$#',
        '#/app/Controllers/[^/]+\.php$#',
        '#/index\.php$#',
    ];

    foreach ($patterns as $pattern) {
        $base = preg_replace($pattern, '', $scriptName);
        if ($base !== $scriptName) {
            return rtrim($base, '/');
        }
    }

    return '';
}

function app_url(string $path = ''): string
{
    $base = app_base_path();
    $path = '/' . ltrim($path, '/');

    return $base . ($path === '/' ? '/' : $path);
}

function asset_url(string $path): string
{
    return app_url('public/' . ltrim($path, '/'));
}

function view_url(string $view): string
{
    $slug = preg_replace('/\.php$/', '', ltrim($view, '/'));

    return app_url($slug);
}

function redirect_url(string $path): string
{
    return app_url($path);
}

function start_url_rewriter(): void
{
    if (defined('URL_REWRITER_STARTED')) {
        return;
    }

    define('URL_REWRITER_STARTED', true);

    ob_start(function (string $html): string {
        $base = app_base_path();
        if ($base === '') {
            return $html;
        }

        $html = preg_replace('#\b(href|src|action)="/(?!/)#', '$1="' . $base . '/', $html);
        $html = preg_replace("#url\('/(?!/)#", "url('" . $base . '/', $html);
        $html = preg_replace('#url\("/(?!/)#', 'url("' . $base . '/', $html);

        return $html;
    });
}
