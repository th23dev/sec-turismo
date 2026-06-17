<?php 
class VideoModel {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function buscarVideos($search = '') {
        $sql = "SELECT * FROM videos WHERE titulo LIKE ? OR descricao LIKE ? ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $like = "%{$search}%";
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    public function buscarVideo($id) {
        $sql = "SELECT * FROM videos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criarVideo($titulo, $descricao, $video) {
        $sql = "INSERT INTO videos (titulo, descricao, video) VALUES (:titulo, :descricao, :video)";
        try {
            return $this->executarInsertVideo($sql, $titulo, $descricao, $video);
        } catch (PDOException $e) {
            if ($e->getCode() !== '42S02') {
                return false;
            }

            $this->garantirTabelaVideos();
            return $this->executarInsertVideo($sql, $titulo, $descricao, $video);
        }
    }

    private function executarInsertVideo($sql, $titulo, $descricao, $video) {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindValue(':video', $video, PDO::PARAM_STR);

        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    public function atualizarVideo($id, $titulo, $descricao, $video) {
        $sql = "UPDATE videos SET titulo = :titulo, descricao = :descricao, video = :video WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->bindValue(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindValue(':video', $video, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function excluirVideo($id) {
        $sql = "DELETE FROM videos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function garantirTabelaVideos() {
        $sql = "CREATE TABLE IF NOT EXISTS videos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            descricao TEXT NULL,
            video TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->exec($sql);
    }
}
?>
