<?php 
class VideoModel {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function buscarVideos($search = '') {
        $sql = "SELECT * FROM videos WHERE titulo LIKE ? OR descricao LIKE ?";
        $stmt = $this->db->prepare($sql);
        $like = "%{$search}%";
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }
}
?>
