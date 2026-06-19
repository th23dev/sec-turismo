<?php

class ImageUpload
{
    private const MAX_FILE_SIZE = 5 * 1024 * 1024;
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    private const UPLOAD_DIR_WEB = '/public/imgs/uploads/';

    private const MAX_MEDIA_FILE_SIZE = 50 * 1024 * 1024;
    private const ALLOWED_MEDIA_TYPES = [
        'image/jpeg', 'image/png', 'image/webp', 'image/gif',
        'video/mp4', 'video/webm', 'video/quicktime', 'video/ogg', 'video/x-matroska',
    ];
    private const ALLOWED_MEDIA_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'webm', 'mov', 'ogg', 'mkv'];

    private static function getUploadDir(): string
    {
        return dirname(__DIR__, 2) . '/public/imgs/uploads/';
    }

    private static function safeSubfolder(string $subfolder): string
    {
        $subfolder = trim(str_replace('\\', '/', $subfolder), '/');
        if ($subfolder === '') {
            return '';
        }

        if (!preg_match('#^[A-Za-z0-9_-]+(?:/[A-Za-z0-9_-]+)*$#', $subfolder)) {
            return '';
        }

        return $subfolder;
    }

    private static function randomFilename(string $prefix, string $extension): string
    {
        return $prefix . bin2hex(random_bytes(16)) . '.' . strtolower($extension);
    }

    private static function isSafeRemoteUrl(string $url, array $allowedExtensions): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH);
        $ext = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));

        return $ext !== '' && in_array($ext, $allowedExtensions, true);
    }

    private static function prepareUploadDir(string $subfolder): array|false
    {
        $subfolder = self::safeSubfolder($subfolder);
        $uploadPath = self::getUploadDir();

        if ($subfolder !== '') {
            $uploadPath .= $subfolder . '/';
        }

        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true)) {
            return false;
        }

        return [$uploadPath, $subfolder];
    }

    public static function upload($file, $subfolder = '')
    {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if (($file['size'] ?? 0) > self::MAX_FILE_SIZE) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::ALLOWED_TYPES, true)) {
            return false;
        }

        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            return false;
        }

        $prepared = self::prepareUploadDir((string) $subfolder);
        if ($prepared === false) {
            return false;
        }
        [$uploadPath, $safeSubfolder] = $prepared;

        $filename = self::randomFilename('img_', $ext);
        if (!move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {
            return false;
        }

        return self::UPLOAD_DIR_WEB . ($safeSubfolder !== '' ? $safeSubfolder . '/' : '') . $filename;
    }

    public static function uploadMedia($file, $subfolder = '')
    {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if (($file['size'] ?? 0) > self::MAX_MEDIA_FILE_SIZE) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::ALLOWED_MEDIA_TYPES, true)) {
            return false;
        }

        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_MEDIA_EXTENSIONS, true)) {
            return false;
        }

        $prepared = self::prepareUploadDir((string) $subfolder);
        if ($prepared === false) {
            return false;
        }
        [$uploadPath, $safeSubfolder] = $prepared;

        $filename = self::randomFilename('media_', $ext);
        if (!move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {
            return false;
        }

        return self::UPLOAD_DIR_WEB . ($safeSubfolder !== '' ? $safeSubfolder . '/' : '') . $filename;
    }

    public static function validateUrl($url)
    {
        if (empty($url)) {
            return false;
        }

        return self::isSafeRemoteUrl($url, self::ALLOWED_EXTENSIONS);
    }

    public static function validateMediaUrl($url)
    {
        if (empty($url)) {
            return false;
        }

        return self::isSafeRemoteUrl($url, self::ALLOWED_MEDIA_EXTENSIONS);
    }

    public static function delete($image_path)
    {
        if (empty($image_path)) {
            return false;
        }

        $uploadsPos = strpos(str_replace('\\', '/', $image_path), 'imgs/uploads/');
        if ($uploadsPos === false) {
            return false;
        }

        $relativeUploadPath = substr(str_replace('\\', '/', $image_path), $uploadsPos + strlen('imgs/uploads/'));
        $basePath = realpath(self::getUploadDir());
        if ($basePath === false) {
            return false;
        }

        $fullPath = realpath(self::getUploadDir() . $relativeUploadPath);
        if ($fullPath === false || strpos($fullPath, $basePath . DIRECTORY_SEPARATOR) !== 0) {
            return false;
        }

        return is_file($fullPath) && unlink($fullPath);
    }
}
