<?php
require_once __DIR__ . '/../Models/ConteudoTuristicoModel.php';
require_once __DIR__ . '/../Utils/ImageUpload.php';
require_once __DIR__ . '/../Utils/security.php';

class ConteudoTuristicoController
{
    private ConteudoTuristicoModel $model;
    private string $lastError = '';

    public function __construct(PDO $conexao)
    {
        $this->model = new ConteudoTuristicoModel($conexao);
    }

    public function categorias(): array
    {
        return $this->model->categorias();
    }

    public function categoriaInfo(string $categoria): ?array
    {
        return $this->model->categoriaInfo($categoria);
    }

    public function lastError(): string
    {
        return $this->lastError;
    }

    public function buscarTodos(string $categoria, string $search = ''): array
    {
        return $this->model->categoriaExiste($categoria) ? $this->model->buscarTodos($categoria, $search) : [];
    }

    public function buscar(string $categoria, int $id): ?array
    {
        return $this->model->categoriaExiste($categoria) ? $this->model->buscar($categoria, $id) : null;
    }

    public function criar(string $categoria, array $dados, array $arquivos = []): int|false
    {
        $this->lastError = '';
        $dadosPreparados = $this->prepararDados($categoria, $dados, $arquivos);
        if (!$this->model->categoriaExiste($categoria)) {
            $this->lastError = 'Categoria invalida.';
            return false;
        }

        if ($dadosPreparados === false) {
            return false;
        }

        $id = $this->model->criar($categoria, $dadosPreparados['principal']);
        if ($id) {
            $this->model->substituirFotos($categoria, $id, $dadosPreparados['fotos']);
        }

        if (!$id) {
            $this->lastError = 'Nao foi possivel salvar no banco de dados.';
        }

        return $id;
    }

    public function atualizar(string $categoria, int $id, array $dados, array $arquivos = []): bool
    {
        $this->lastError = '';
        if (!$this->model->categoriaExiste($categoria)) {
            $this->lastError = 'Categoria invalida.';
            return false;
        }

        $atual = $this->model->buscar($categoria, $id);
        if (!$atual) {
            $this->lastError = 'Cadastro nao encontrado.';
            return false;
        }

        $dadosPreparados = $this->prepararDados($categoria, $dados, $arquivos, $atual['imagem_principal'] ?? '');
        if ($dadosPreparados === false) {
            return false;
        }

        $ok = $this->model->atualizar($categoria, $id, $dadosPreparados['principal']);
        if ($ok) {
            $this->model->substituirFotos($categoria, $id, $dadosPreparados['fotos']);
        } else {
            $this->lastError = 'Nao foi possivel salvar no banco de dados.';
        }

        return $ok;
    }

    public function excluir(string $categoria, int $id): bool
    {
        if ($id <= 0) {
            return false;
        }

        return $this->model->categoriaExiste($categoria) && $this->model->excluir($categoria, $id);
    }

    public function buscarPratos(int $restauranteId): array
    {
        return $this->model->buscarPratos($restauranteId);
    }

    public function buscarPrato(int $restauranteId, int $pratoId): ?array
    {
        if ($restauranteId <= 0 || $pratoId <= 0) {
            return null;
        }

        return $this->model->buscarPrato($restauranteId, $pratoId);
    }

    public function criarPrato(int $restauranteId, array $dados, array $arquivos = []): int|false
    {
        $prato = $this->prepararPrato($dados, $arquivos);
        if ($prato === false) {
            return false;
        }

        return $this->model->criarPrato($restauranteId, $prato);
    }

    public function atualizarPrato(int $restauranteId, int $pratoId, array $dados, array $arquivos = []): bool
    {
        if (!$this->buscarPrato($restauranteId, $pratoId)) {
            return false;
        }

        $prato = $this->prepararPrato($dados, $arquivos);
        if ($prato === false) {
            return false;
        }

        return $this->model->atualizarPrato($restauranteId, $pratoId, $prato);
    }

    public function excluirPrato(int $restauranteId, int $pratoId): bool
    {
        if ($restauranteId <= 0 || $pratoId <= 0) {
            return false;
        }

        return $this->model->excluirPrato($restauranteId, $pratoId);
    }

    private function prepararDados(string $categoria, array $dados, array $arquivos = [], string $imagemAtual = ''): array|false
    {
        $imagem = $this->prepararImagemPrincipal($dados, $arquivos, $imagemAtual);
        if ($imagem === false) {
            $this->lastError = 'A imagem principal deve ser um arquivo JPG, PNG, WEBP ou GIF de ate 5 MB, ou uma URL direta terminada em jpg, jpeg, png, webp ou gif.';
            return false;
        }

        $siteUrl = trim($dados['site_url'] ?? '');
        $instagram = trim($dados['instagram'] ?? '');
        $mapsUrl = extract_iframe_src((string) ($dados['google_maps_url'] ?? $dados['trajeto_maps_url'] ?? ''));

        if (!is_safe_http_url($siteUrl)) {
            $this->lastError = 'O link do site precisa ser uma URL completa iniciando com http:// ou https://.';
            return false;
        }

        if (!is_safe_http_url($instagram)) {
            $this->lastError = 'O Instagram precisa ser o link completo do perfil, por exemplo https://instagram.com/restaurante.';
            return false;
        }

        if (!is_safe_google_maps_url($mapsUrl)) {
            $this->lastError = 'A localizacao precisa ser um link ou iframe valido do Google Maps.';
            return false;
        }

        $principal = match ($categoria) {
            'gastronomia' => [
                'nome' => trim($dados['nome'] ?? ''),
                'imagem_principal' => $imagem,
                'instagram' => $instagram,
                'numero' => $this->normalizarTelefone($dados['numero'] ?? ''),
                'site_url' => $siteUrl,
                'google_maps_url' => $mapsUrl,
                'descricao' => trim($dados['descricao'] ?? ''),
                'horario_funcionamento' => trim($dados['horario_funcionamento'] ?? ''),
            ],
            'manguezais' => [
                'titulo' => trim($dados['titulo'] ?? ''),
                'imagem_principal' => $imagem,
                'google_maps_url' => $mapsUrl,
                'descricao' => trim($dados['descricao'] ?? ''),
            ],
            'cultura_popular' => [
                'titulo' => trim($dados['titulo'] ?? ''),
                'imagem_principal' => $imagem,
                'descricao' => trim($dados['descricao'] ?? ''),
            ],
            'trilha' => [
                'titulo' => trim($dados['titulo'] ?? ''),
                'imagem_principal' => $imagem,
                'trajeto_maps_url' => $mapsUrl,
                'descricao' => trim($dados['descricao'] ?? ''),
                'instagram' => $instagram,
                'site_url' => $siteUrl,
            ],
            default => false,
        };

        if ($principal === false) {
            return false;
        }

        return [
            'principal' => $principal,
            'fotos' => $this->prepararFotosSecundarias($dados, $arquivos),
            'pratos' => [],
        ];
    }

    private function prepararPrato(array $dados, array $arquivos = []): array|false
    {
        $foto = trim($dados['prato_foto_url'] ?? '');
        if ($foto !== '' && !$this->validarImagemReferencia($foto)) {
            return false;
        }

        if (!empty($arquivos['prato_foto_arquivo']['name'])) {
            $uploaded = ImageUpload::upload($arquivos['prato_foto_arquivo']);
            if (!$uploaded) {
                return false;
            }
            $foto = $uploaded;
        }

        $prato = [
            'foto' => $foto,
            'nome' => trim($dados['prato_nome'] ?? ''),
            'descricao' => trim($dados['prato_descricao'] ?? ''),
        ];

        if ($prato['nome'] === '' && $prato['descricao'] === '' && $prato['foto'] === '') {
            return false;
        }

        return $prato;
    }

    private function prepararImagemPrincipal(array $dados, array $arquivos, string $imagemAtual): string|false
    {
        if (!empty($arquivos['arquivo_imagem']['name'])) {
            return ImageUpload::upload($arquivos['arquivo_imagem']) ?: false;
        }

        if (isset($dados['imagem_principal_url']) && trim($dados['imagem_principal_url']) !== '') {
            $imagem = trim($dados['imagem_principal_url']);
            return $this->validarImagemReferencia($imagem) ? $imagem : false;
        }

        return $imagemAtual;
    }

    private function prepararFotosSecundarias(array $dados, array $arquivos): array
    {
        $entrada = $dados['fotos_secundarias'] ?? [];
        $urls = is_array($entrada)
            ? array_map('trim', $entrada)
            : array_map('trim', explode("\n", (string) $entrada));
        $fotos = [];

        foreach ($urls as $url) {
            if ($this->validarImagemReferencia($url)) {
                $fotos[] = $url;
            }
        }

        foreach ($this->normalizarArquivos($arquivos['fotos_arquivos'] ?? []) as $file) {
            $uploaded = ImageUpload::upload($file);
            if ($uploaded) {
                $fotos[] = $uploaded;
            }
        }

        return array_values(array_unique($fotos));
    }

    private function validarImagemReferencia(string $url): bool
    {
        $url = trim($url);
        if ($url === '') {
            return false;
        }

        if (ImageUpload::validateUrl($url)) {
            return true;
        }

        if (str_contains($url, '..')) {
            return false;
        }

        return preg_match('#^/public/imgs/uploads/[A-Za-z0-9_./-]+\.(jpe?g|png|webp|gif)$#i', $url) === 1;
    }

    private function normalizarTelefone(string $numero): string
    {
        $numero = trim($numero);
        $numero = preg_replace('/[^\d\s()+\-.]/', '', $numero) ?? '';
        $numero = preg_replace('/\s+/', ' ', $numero) ?? '';

        return mb_substr(trim($numero), 0, 60);
    }

    private function normalizarArquivos(array $files): array
    {
        if (empty($files['name'])) {
            return [];
        }

        if (!is_array($files['name'])) {
            return (($files['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) ? [] : [$files];
        }

        $normalizados = [];
        foreach ($files['name'] as $index => $name) {
            $error = $files['error'][$index] ?? UPLOAD_ERR_NO_FILE;
            if ($name === '' || $error === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $normalizados[$index] = [
                'name' => $files['name'][$index],
                'type' => $files['type'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error' => $error,
                'size' => $files['size'][$index] ?? 0,
            ];
        }

        return $normalizados;
    }
}
