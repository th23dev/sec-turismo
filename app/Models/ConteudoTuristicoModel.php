<?php
class ConteudoTuristicoModel
{
    private PDO $db;

    private array $categorias = [
        'gastronomia' => [
            'table' => 'restaurantes',
            'media_table' => 'restaurante_fotos',
            'titulo' => 'Restaurantes',
            'icone' => 'fa-utensils',
            'title_column' => 'nome',
        ],
        'manguezais' => [
            'table' => 'manguezais',
            'media_table' => 'manguezal_fotos',
            'titulo' => 'Manguezais',
            'icone' => 'fa-seedling',
            'title_column' => 'titulo',
        ],
        'cultura_popular' => [
            'table' => 'cultura_popular',
            'media_table' => 'cultura_popular_fotos',
            'titulo' => 'Cultura Popular',
            'icone' => 'fa-drum',
            'title_column' => 'titulo',
        ],
        'trilha' => [
            'table' => 'trilhas',
            'media_table' => 'trilha_fotos',
            'titulo' => 'Trilhas',
            'icone' => 'fa-route',
            'title_column' => 'titulo',
        ],
    ];

    public function __construct(PDO $conexao)
    {
        $this->db = $conexao;
    }

    public function categorias(): array
    {
        return $this->categorias;
    }

    public function categoriaExiste(string $categoria): bool
    {
        return isset($this->categorias[$categoria]);
    }

    public function categoriaInfo(string $categoria): ?array
    {
        return $this->categorias[$categoria] ?? null;
    }

    private function table(string $categoria): string
    {
        if (!$this->categoriaExiste($categoria)) {
            throw new InvalidArgumentException('Categoria inválida.');
        }

        return $this->categorias[$categoria]['table'];
    }

    private function publicPath(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        if (preg_match('#^(https?://|/)#i', $path)) {
            return $path;
        }

        return '/' . ltrim(str_replace(['../../public/', '../public/', 'public/'], 'public/', $path), '/');
    }

    private function normalize(array $item): array
    {
        if (isset($item['imagem_principal'])) {
            $item['imagem_principal'] = $this->publicPath($item['imagem_principal']);
        }

        return $item;
    }

    public function buscarTodos(string $categoria, string $search = ''): array
    {
        $table = $this->table($categoria);
        $titleColumn = $this->categorias[$categoria]['title_column'];
        $where = $titleColumn ? "{$titleColumn} LIKE :search_titulo OR descricao LIKE :search_descricao" : "descricao LIKE :search_descricao";
        $order = $titleColumn ?: 'id';
        $sql = "SELECT * FROM {$table} WHERE {$where} ORDER BY {$order}";
        $stmt = $this->db->prepare($sql);
        if ($titleColumn) {
            $stmt->bindValue(':search_titulo', "%{$search}%", PDO::PARAM_STR);
        }
        $stmt->bindValue(':search_descricao', "%{$search}%", PDO::PARAM_STR);
        $stmt->execute();

        $itens = array_map(fn ($item) => $this->normalize($item), $stmt->fetchAll(PDO::FETCH_ASSOC));
        foreach ($itens as &$item) {
            $item['fotos'] = $this->buscarFotos($categoria, (int) $item['id']);
            if ($categoria === 'gastronomia') {
                $item['pratos'] = $this->buscarPratos((int) $item['id']);
            }
        }

        return $itens;
    }

    public function buscar(string $categoria, int $id): ?array
    {
        $table = $this->table($categoria);
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return null;
        }

        $item = $this->normalize($item);
        $item['fotos'] = $this->buscarFotos($categoria, (int) $item['id']);
        if ($categoria === 'gastronomia') {
            $item['pratos'] = $this->buscarPratos((int) $item['id']);
        }

        return $item;
    }

    public function criar(string $categoria, array $dados): int|false
    {
        $table = $this->table($categoria);
        [$columns, $params] = $this->columnsParams($categoria);
        $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $params) . ")";
        $stmt = $this->db->prepare($sql);
        $this->bindDados($stmt, $categoria, $dados);

        return $stmt->execute() ? (int) $this->db->lastInsertId() : false;
    }

    public function atualizar(string $categoria, int $id, array $dados): bool
    {
        $table = $this->table($categoria);
        [$columns] = $this->columnsParams($categoria);
        $sets = array_map(fn ($column) => "{$column} = :{$column}", $columns);
        $sql = "UPDATE {$table} SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $this->bindDados($stmt, $categoria, $dados);

        return $stmt->execute();
    }

    public function excluir(string $categoria, int $id): bool
    {
        $table = $this->table($categoria);
        $stmt = $this->db->prepare("DELETE FROM {$table} WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function substituirFotos(string $categoria, int $itemId, array $urls): void
    {
        $mediaTable = $this->categorias[$categoria]['media_table'];
        $foreignKey = $this->foreignKey($categoria);
        $delete = $this->db->prepare("DELETE FROM {$mediaTable} WHERE {$foreignKey} = :id");
        $delete->bindValue(':id', $itemId, PDO::PARAM_INT);
        $delete->execute();

        foreach ($urls as $url) {
            if (trim($url) === '') {
                continue;
            }
            $insert = $this->db->prepare("INSERT INTO {$mediaTable} ({$foreignKey}, url) VALUES (:id, :url)");
            $insert->bindValue(':id', $itemId, PDO::PARAM_INT);
            $insert->bindValue(':url', $url, PDO::PARAM_STR);
            $insert->execute();
        }
    }

    public function criarPrato(int $restauranteId, array $prato): int|false
    {
        $stmt = $this->db->prepare("INSERT INTO restaurante_pratos (restaurante_id, foto, nome, descricao) VALUES (:id, :foto, :nome, :descricao)");
        $stmt->bindValue(':id', $restauranteId, PDO::PARAM_INT);
        $stmt->bindValue(':foto', $prato['foto'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':nome', $prato['nome'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $prato['descricao'] ?? '', PDO::PARAM_STR);

        return $stmt->execute() ? (int) $this->db->lastInsertId() : false;
    }

    public function buscarPrato(int $restauranteId, int $pratoId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM restaurante_pratos WHERE id = :prato_id AND restaurante_id = :restaurante_id");
        $stmt->bindValue(':prato_id', $pratoId, PDO::PARAM_INT);
        $stmt->bindValue(':restaurante_id', $restauranteId, PDO::PARAM_INT);
        $stmt->execute();
        $prato = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$prato) {
            return null;
        }

        $prato['foto'] = $this->publicPath($prato['foto'] ?? '');

        return $prato;
    }

    public function atualizarPrato(int $restauranteId, int $pratoId, array $prato): bool
    {
        $stmt = $this->db->prepare("UPDATE restaurante_pratos SET foto = :foto, nome = :nome, descricao = :descricao WHERE id = :prato_id AND restaurante_id = :restaurante_id");
        $stmt->bindValue(':prato_id', $pratoId, PDO::PARAM_INT);
        $stmt->bindValue(':restaurante_id', $restauranteId, PDO::PARAM_INT);
        $stmt->bindValue(':foto', $prato['foto'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':nome', $prato['nome'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $prato['descricao'] ?? '', PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function excluirPrato(int $restauranteId, int $pratoId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM restaurante_pratos WHERE id = :prato_id AND restaurante_id = :restaurante_id");
        $stmt->bindValue(':prato_id', $pratoId, PDO::PARAM_INT);
        $stmt->bindValue(':restaurante_id', $restauranteId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    private function buscarFotos(string $categoria, int $itemId): array
    {
        $mediaTable = $this->categorias[$categoria]['media_table'];
        $foreignKey = $this->foreignKey($categoria);
        $stmt = $this->db->prepare("SELECT * FROM {$mediaTable} WHERE {$foreignKey} = :id ORDER BY id");
        $stmt->bindValue(':id', $itemId, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(function ($foto) {
            $foto['url'] = $this->publicPath($foto['url'] ?? '');
            return $foto;
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function buscarPratos(int $restauranteId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM restaurante_pratos WHERE restaurante_id = :id ORDER BY id");
        $stmt->bindValue(':id', $restauranteId, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(function ($prato) {
            $prato['foto'] = $this->publicPath($prato['foto'] ?? '');
            return $prato;
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function foreignKey(string $categoria): string
    {
        return match ($categoria) {
            'gastronomia' => 'restaurante_id',
            'manguezais' => 'manguezal_id',
            'cultura_popular' => 'cultura_popular_id',
            'trilha' => 'trilha_id',
            default => 'item_id',
        };
    }

    private function columnsParams(string $categoria): array
    {
        $columns = match ($categoria) {
            'gastronomia' => ['nome', 'imagem_principal', 'instagram', 'numero', 'site_url', 'google_maps_url', 'descricao', 'horario_funcionamento'],
            'manguezais' => ['titulo', 'imagem_principal', 'google_maps_url', 'descricao'],
            'cultura_popular' => ['titulo', 'imagem_principal', 'descricao'],
            'trilha' => ['titulo', 'imagem_principal', 'trajeto_maps_url', 'descricao', 'instagram', 'site_url'],
            default => ['descricao'],
        };

        return [$columns, array_map(fn ($column) => ':' . $column, $columns)];
    }

    private function bindDados(PDOStatement $stmt, string $categoria, array $dados): void
    {
        [$columns] = $this->columnsParams($categoria);
        foreach ($columns as $column) {
            $stmt->bindValue(':' . $column, $dados[$column] ?? '', PDO::PARAM_STR);
        }
    }
}
