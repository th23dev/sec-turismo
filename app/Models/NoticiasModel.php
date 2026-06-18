<?php
class NoticiasModel
{
    private $db;

    public function __construct($conexao)
    {
        $this->db = $conexao;
    }

    private function publicPath(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        if (preg_match('#^(https?://|data:|/)#i', $path)) {
            return $path;
        }

        return '/' . ltrim(str_replace(['../../public/', '../public/', 'public/'], 'public/', $path), '/');
    }

    private function normalizeNoticia(array $noticia): array
    {
        if (isset($noticia['imagem_url'])) {
            $noticia['imagem_url'] = $this->publicPath($noticia['imagem_url']);
        }

        return $noticia;
    }

    public function criarNoticia($titulo, $conteudo, $imagem_url, $instagram_url = '', $data_inicio = null, $data_fim = null, $indefinido = 0)
    {
        $sql = "INSERT INTO noticias (titulo, conteudo, imagem_url, instagram_url, data_inicio, data_fim, indefinido, published_at) VALUES (:titulo, :conteudo, :imagem_url, :instagram_url, :data_inicio, :data_fim, :indefinido, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindValue(':conteudo', $conteudo, PDO::PARAM_STR);
        $stmt->bindValue(':imagem_url', $imagem_url, PDO::PARAM_STR);
        $stmt->bindValue(':instagram_url', $instagram_url, PDO::PARAM_STR);
        $stmt->bindValue(':data_inicio', $data_inicio ?: null, PDO::PARAM_STR);
        $stmt->bindValue(':data_fim', $data_fim ?: null, PDO::PARAM_STR);
        $stmt->bindValue(':indefinido', $indefinido, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function atualizarNoticia($id, $titulo, $conteudo, $imagem_url, $instagram_url = '', $data_inicio = null, $data_fim = null, $indefinido = 0)
    {
        $sql = "UPDATE noticias SET titulo = :titulo, conteudo = :conteudo, imagem_url = :imagem_url, instagram_url = :instagram_url, data_inicio = :data_inicio, data_fim = :data_fim, indefinido = :indefinido, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindValue(':conteudo', $conteudo, PDO::PARAM_STR);
        $stmt->bindValue(':imagem_url', $imagem_url, PDO::PARAM_STR);
        $stmt->bindValue(':instagram_url', $instagram_url, PDO::PARAM_STR);
        $stmt->bindValue(':data_inicio', $data_inicio ?: null, PDO::PARAM_STR);
        $stmt->bindValue(':data_fim', $data_fim ?: null, PDO::PARAM_STR);
        $stmt->bindValue(':indefinido', $indefinido, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function buscarNoticias()
    {
        $sql = "SELECT * FROM noticias ORDER BY published_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return array_map(fn ($noticia) => $this->normalizeNoticia($noticia), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function buscarUltimasNoticias($limite = 3)
    {
        $sql = "SELECT * FROM noticias WHERE (data_inicio IS NULL OR data_inicio <= NOW()) AND (data_fim IS NULL OR data_fim >= NOW()) ORDER BY published_at DESC LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
        $stmt->execute();
        $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($noticias) < $limite) {
            $faltam = $limite - count($noticias);
            $excludedIds = array_column($noticias, 'id');
            if (!empty($excludedIds)) {
                $placeholders = implode(',', array_fill(0, count($excludedIds), '?'));
                $sql = "SELECT * FROM noticias WHERE id NOT IN ($placeholders) ORDER BY published_at DESC LIMIT ?";
                $stmt = $this->db->prepare($sql);
                foreach ($excludedIds as $index => $id) {
                    $stmt->bindValue($index + 1, (int) $id, PDO::PARAM_INT);
                }
                $stmt->bindValue(count($excludedIds) + 1, (int) $faltam, PDO::PARAM_INT);
            } else {
                $sql = "SELECT * FROM noticias ORDER BY published_at DESC LIMIT ?";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(1, (int) $faltam, PDO::PARAM_INT);
            }
            $stmt->execute();
            $fallback = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $noticias = array_merge($noticias, $fallback);
        }

        return array_map(fn ($noticia) => $this->normalizeNoticia($noticia), $noticias);
    }

    public function buscarNoticia($id)
    {
        $sql = "SELECT * FROM noticias WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $noticia = $stmt->fetch(PDO::FETCH_ASSOC);

        return $noticia ? $this->normalizeNoticia($noticia) : $noticia;
    }

    public function excluirNoticia($id)
    {
        $sql = "DELETE FROM noticias WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
