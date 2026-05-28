<?php
class ImageUpload
{
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    private const UPLOAD_DIR_WEB = '../../public/imgs/uploads/';

    private static function getUploadDir(): string
    {
        return dirname(__DIR__, 2) . '/public/imgs/uploads/';
    }

    /**
     * Processa upload de arquivo de imagem
     * @param array $file - $_FILES['campo']
     * @param string $subfolder - pasta dentro de uploads (ex: 'praias', 'hoteis')
     * @return string|false - caminho relativo da imagem ou false se falhar
     */
    public static function upload($file, $subfolder = '')
    {
        // Validações básicas
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Validar tamanho
        if ($file['size'] > self::MAX_FILE_SIZE) {
            return false;
        }

        // Validar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, self::ALLOWED_TYPES)) {
            return false;
        }

        // Validar extensão
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS)) {
            return false;
        }

        // Criar diretório se não existir
        $upload_path = self::getUploadDir();
        if (!empty($subfolder)) {
            $upload_path .= rtrim($subfolder, '/') . '/';
        }

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        // Gerar nome único para o arquivo
        $filename = uniqid('img_', true) . '.' . $ext;
        $full_path = $upload_path . $filename;

        // Mover arquivo
        if (move_uploaded_file($file['tmp_name'], $full_path)) {
            // Retornar caminho acessível para a view
            $relative_path = self::UPLOAD_DIR_WEB;
            if (!empty($subfolder)) {
                $relative_path .= rtrim($subfolder, '/') . '/';
            }
            $relative_path .= $filename;
            return $relative_path;
        }

        return false;
    }

    /**
     * Valida se uma URL é uma imagem válida
     * @param string $url - URL da imagem
     * @return bool
     */
    public static function validateUrl($url)
    {
        if (empty($url)) {
            return false;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Verificar extensão
        $path = parse_url($url, PHP_URL_PATH);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        return in_array($ext, self::ALLOWED_EXTENSIONS);
    }

    /**
     * Deleta arquivo de imagem
     * @param string $image_path - caminho relativo da imagem
     * @return bool
     */
    public static function delete($image_path)
    {
        if (empty($image_path) || strpos($image_path, 'uploads') === false) {
            return false;
        }

        $uploadsPos = strpos($image_path, 'imgs/uploads/');
        if ($uploadsPos === false) {
            return false;
        }

        $relativeUploadPath = substr($image_path, $uploadsPos);
        $full_path = dirname(__DIR__, 2) . '/public/' . $relativeUploadPath;
        
        if (file_exists($full_path)) {
            return unlink($full_path);
        }

        return false;
    }
}
?>
