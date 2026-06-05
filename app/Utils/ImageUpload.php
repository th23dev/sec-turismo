<?php
class ImageUpload
{
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    private const UPLOAD_DIR_WEB = '../../public/imgs/uploads/';

    // Media (images + videos)
    private const MAX_MEDIA_FILE_SIZE = 50 * 1024 * 1024; // 50MB
    private const ALLOWED_MEDIA_TYPES = [
        'image/jpeg', 'image/png', 'image/webp', 'image/gif',
        'video/mp4', 'video/webm', 'video/quicktime', 'video/ogg', 'video/x-matroska'
    ];
    private const ALLOWED_MEDIA_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'webm', 'mov', 'ogg', 'mkv'];

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
     * Processa upload de mídia (imagem ou vídeo)
     * @param array $file - $_FILES['campo']
     * @param string $subfolder - pasta dentro de uploads
     * @return string|false - caminho relativo da mídia ou false se falhar
     */
    public static function uploadMedia($file, $subfolder = '')
    {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Validar tamanho
        if ($file['size'] > self::MAX_MEDIA_FILE_SIZE) {
            return false;
        }

        // Validar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, self::ALLOWED_MEDIA_TYPES)) {
            return false;
        }

        // Validar extensão
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_MEDIA_EXTENSIONS)) {
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
        $filename = uniqid('media_', true) . '.' . $ext;
        $full_path = $upload_path . $filename;

        if (move_uploaded_file($file['tmp_name'], $full_path)) {
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
     * Valida URL de mídia (imagem ou vídeo)
     * Aceita tanto URLs com extensão quanto URLs dinâmicas
     * @param string $url
     * @return bool
     */
    public static function validateMediaUrl($url)
    {
        if (empty($url)) {
            return false;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Se a URL tem extensão conhecida, valida-a
        $path = parse_url($url, PHP_URL_PATH);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (!empty($ext)) {
            // URL tem extensão - valida contra lista de extensões permitidas
            return in_array($ext, self::ALLOWED_MEDIA_EXTENSIONS);
        }

        // URL sem extensão no path - é uma URL dinâmica (ex: API)
        // Tenta fazer HEAD request para verificar se é uma imagem/vídeo válido
        // Se falhar, apenas aceita (confia no usuário)
        $context = stream_context_create([
            'http' => [
                'method' => 'HEAD',
                'timeout' => 5,
                'ignore_errors' => true
            ]
        ]);
        
        $headers = get_headers($url, 1, $context);
        if ($headers && isset($headers['Content-Type'])) {
            $contentType = $headers['Content-Type'];
            if (is_array($contentType)) {
                $contentType = array_pop($contentType);
            }
            
            $allowedMimes = [
                'image/jpeg', 'image/png', 'image/webp', 'image/gif',
                'video/mp4', 'video/webm', 'video/quicktime', 'video/ogg', 'video/x-matroska'
            ];
            
            return in_array($contentType, $allowedMimes);
        }
        
        // Se não conseguir fazer HEAD request, aceita a URL (confia no usuário)
        return true;
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
