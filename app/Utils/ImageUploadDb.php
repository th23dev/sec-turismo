<?php

class ImageUploadDb
{
    /**
     * Retorna dados binários + metadados a partir de um upload (string binária segura).
     * Usa o tmp_name para leitura.
     */
    public static function fromUploadedFile(array $file): array|false
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return false;
        }

        // Tamanho e validações são feitas no ImageUpload.php original.
        // Aqui assumimos que você já filtrou tipos/size antes (ou vamos manter simples).
        $binary = @file_get_contents($file['tmp_name']);
        if ($binary === false) {
            return false;
        }

        $mime = null;
        $finfo = @finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = @finfo_file($finfo, $file['tmp_name']);
            @finfo_close($finfo);
        }

        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

        return [
            'binary' => $binary,
            'mime' => $mime ?: 'application/octet-stream',
            'ext' => $ext,
        ];
    }
}


