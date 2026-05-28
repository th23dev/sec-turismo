<?php
// SIMPLIFICAÇÃO GERAL: Implementar um framework MVC como Laravel para melhor organização do código, usar bibliotecas de autenticação seguras, e ORM para abstrair consultas ao banco de dados.
class LugaresModel
{
    private $db;

    public function __construct($conexao)
    {
        $this->db = $conexao;
    }

    public function criarLocal($imagem_principal, $nome, $tipo, $numero, $instagram, $linkInstagram, $descricao, $possui_restaurante)
    {
        $sql = "INSERT INTO lugares (imagem_principal, nome, tipo, numero, instagram, linkInstagram, descricao, possui_restaurante) 
                VALUES (:imagem_principal, :nome, :tipo, :numero, :instagram, :linkInstagram, :descricao, :possui_restaurante)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':imagem_principal', $imagem_principal, PDO::PARAM_STR);
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':numero', $numero, PDO::PARAM_STR);
        $stmt->bindValue(':instagram', $instagram, PDO::PARAM_STR);
        $stmt->bindValue(':linkInstagram', $linkInstagram, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindValue(':possui_restaurante', $possui_restaurante, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function atualizarLocal($id, $imagem_principal, $nome, $tipo, $numero, $instagram, $linkInstagram, $descricao, $possui_restaurante)
    {
        $sql = "UPDATE lugares 
            SET imagem_principal = :imagem_principal,
                nome = :nome,
                tipo = :tipo,
                numero = :numero,
                instagram = :instagram,
                linkInstagram = :linkInstagram,
                descricao = :descricao,
                possui_restaurante = :possui_restaurante
            WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':imagem_principal', $imagem_principal, PDO::PARAM_STR);
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':numero', $numero, PDO::PARAM_STR);
        $stmt->bindValue(':instagram', $instagram, PDO::PARAM_STR);
        $stmt->bindValue(':linkInstagram', $linkInstagram, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindValue(':possui_restaurante', $possui_restaurante, PDO::PARAM_INT);

        return $stmt->execute();
    }
 

    public function buscarLugares($search = '', $tipo = '')
    {
        $lugaresSql = "SELECT DISTINCT * FROM lugares WHERE (nome LIKE :likeNome OR descricao LIKE :likeDescricao) AND tipo like :tipo ORDER BY nome";
        $lugaresStmt = $this->db->prepare($lugaresSql);
        $lugaresStmt->bindValue(':likeNome', "%{$search}%", PDO::PARAM_STR);
        $lugaresStmt->bindValue(':likeDescricao', "%{$search}%", PDO::PARAM_STR);
        $lugaresStmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $lugaresStmt->execute();
        $lugaresRaw = $lugaresStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($lugaresRaw)) {
            return [];
        }

        // Get all lugar IDs
        $lugarIds = array_column($lugaresRaw, 'id');
        $placeholders = str_repeat('?,', count($lugarIds) - 1) . '?';

        // Fetch all related images
        $midiasSql = "SELECT lugar_id, url FROM midias WHERE lugar_id IN ($placeholders) ORDER BY lugar_id, id";
        $midiasStmt = $this->db->prepare($midiasSql);
        $midiasStmt->execute($lugarIds);
        $midiasRaw = $midiasStmt->fetchAll(PDO::FETCH_ASSOC);

        // Group images properly by lugar_id
        $midiasByLugar = [];
        foreach ($midiasRaw as $midia) {
            $lugarId = $midia['lugar_id'];
            if (!isset($midiasByLugar[$lugarId])) {
                $midiasByLugar[$lugarId] = [];
            }
            $midiasByLugar[$lugarId][] = $midia['url'];
        }

        // Build grouped lugares
        $lugares = [];
        foreach ($lugaresRaw as $lugar) {
            $lugarData = $lugar;
            $lugarId = $lugar['id'];
            $lugarData['url'] = $midiasByLugar[$lugarId] ?? [];
            // Set first image as principal if not set
            if (empty($lugarData['imagem_principal']) && !empty($lugarData['url'])) {
                $lugarData['imagem_principal'] = $lugarData['url'][0];
            }
            $lugares[] = $lugarData;
        }

        return $lugares;
    }

    public function buscarTodosOsLugares()
    {
        $sql = "SELECT DISTINCT * FROM lugares";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarLugar($id)
    {
        $sql = "SELECT DISTINCT * FROM lugares WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function excluirLugar($id)
    {
        $sql = "DELETE FROM lugares WHERE id = :id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    //-------------- Midias --------------

    public function buscarMidias($id){
        $sql = "SELECT * FROM midias WHERE lugar_id = :id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criarMidia($lugar_id, $url){
        $sql = "INSERT INTO midias (lugar_id, url) VALUES (:lugar_id, :url);";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lugar_id', $lugar_id, PDO::PARAM_INT);
        $stmt->bindValue(':url', $url, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function excluirMidia($midia_id){
        $sql = "DELETE FROM midias WHERE id = :id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $midia_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function excluirMidiasPorLugar($lugar_id){
        $sql = "DELETE FROM midias WHERE lugar_id = :lugar_id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lugar_id', $lugar_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>