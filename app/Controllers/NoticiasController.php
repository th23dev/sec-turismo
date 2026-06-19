<?php
require_once __DIR__ . '/../Models/NoticiasModel.php';
require_once __DIR__ . '/../Utils/ImageUpload.php';
require_once __DIR__ . '/../Utils/security.php';

class NoticiasController
{
    private $model;

    public function __construct($conexao)
    {
        $this->model = new NoticiasModel($conexao);
    }

    public function buscarNoticias()
    {
        return $this->model->buscarNoticias();
    }

    public function buscarUltimasNoticias($limite = 3)
    {
        return $this->model->buscarUltimasNoticias($limite);
    }

    public function buscarNoticia($id)
    {
        return $this->model->buscarNoticia($id);
    }

    public function criarNoticia($dados, $arquivos = [])
    {
        $titulo = trim($dados['titulo'] ?? '');
        $conteudo = trim($dados['conteudo'] ?? '');
        $instagram_url = trim($dados['instagram_url'] ?? '');
        $evento_data_inicio = $this->formatarDataSimples($dados['evento_data_inicio'] ?? '');
        $evento_data_fim = $this->formatarDataSimples($dados['evento_data_fim'] ?? '');
        [$evento_data_inicio, $evento_data_fim] = $this->normalizarPeriodoEvento($evento_data_inicio, $evento_data_fim);
        $data_inicio = $this->formatarData($dados['data_inicio'] ?? '');
        $data_fim = $this->formatarData($dados['data_fim'] ?? '');
        $indefinido = isset($dados['indefinido']) && $dados['indefinido'] === '1' ? 1 : 0;

        if (!is_safe_http_url($instagram_url)) {
            return false;
        }

        if ($indefinido) {
            $data_fim = null;
        }

        $imagem_url = $this->processarImagem($dados, $arquivos);

        return $this->model->criarNoticia($titulo, $conteudo, $imagem_url, $instagram_url, $evento_data_inicio, $evento_data_fim, $data_inicio, $data_fim, $indefinido);
    }

    public function atualizarNoticia($id, $dados, $arquivos = [])
    {
        $noticiaExistente = $this->buscarNoticia($id);
        if (!$noticiaExistente) {
            return false;
        }

        $titulo = trim($dados['titulo'] ?? '');
        $conteudo = trim($dados['conteudo'] ?? '');
        $instagram_url = trim($dados['instagram_url'] ?? '');
        $evento_data_inicio = $this->formatarDataSimples($dados['evento_data_inicio'] ?? '');
        $evento_data_fim = $this->formatarDataSimples($dados['evento_data_fim'] ?? '');
        [$evento_data_inicio, $evento_data_fim] = $this->normalizarPeriodoEvento($evento_data_inicio, $evento_data_fim);
        $data_inicio = $this->formatarData($dados['data_inicio'] ?? '');
        $data_fim = $this->formatarData($dados['data_fim'] ?? '');
        $indefinido = isset($dados['indefinido']) && $dados['indefinido'] === '1' ? 1 : 0;

        if (!is_safe_http_url($instagram_url)) {
            return false;
        }

        if ($indefinido) {
            $data_fim = null;
        }

        $imagem_url = $this->processarImagem($dados, $arquivos, $noticiaExistente['imagem_url']);

        return $this->model->atualizarNoticia($id, $titulo, $conteudo, $imagem_url, $instagram_url, $evento_data_inicio, $evento_data_fim, $data_inicio, $data_fim, $indefinido);
    }

    public function excluirNoticia($id)
    {
        $noticiaExistente = $this->buscarNoticia($id);
        if ($noticiaExistente && !empty($noticiaExistente['imagem_url']) && strpos($noticiaExistente['imagem_url'], 'uploads') !== false) {
            ImageUpload::delete($noticiaExistente['imagem_url']);
        }

        return $this->model->excluirNoticia($id);
    }

    private function processarImagem($dados, $arquivos, $imagemExistente = '')
    {
        if (!empty($arquivos['arquivo_imagem']['name'])) {
            $uploaded_path = ImageUpload::upload($arquivos['arquivo_imagem']);
            if ($uploaded_path) {
                if (!empty($imagemExistente) && strpos($imagemExistente, 'uploads') !== false) {
                    ImageUpload::delete($imagemExistente);
                }
                return $uploaded_path;
            }
        }

        $imagem_url = trim($dados['imagem_url'] ?? '');
        if (!empty($imagem_url) && ImageUpload::validateUrl($imagem_url)) {
            return $imagem_url;
        }

        return $imagemExistente;
    }

    private function formatarData($data)
    {
        $data = trim($data);
        if (empty($data)) {
            return null;
        }

        return str_replace('T', ' ', $data);
    }

    private function formatarDataSimples($data)
    {
        $data = trim($data);
        if (empty($data)) {
            return null;
        }

        $timestamp = strtotime($data);
        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }

    private function normalizarPeriodoEvento($inicio, $fim): array
    {
        if (!$inicio && $fim) {
            $inicio = $fim;
            $fim = null;
        }

        if ($inicio && $fim && strtotime($fim) < strtotime($inicio)) {
            return [$fim, $inicio];
        }

        return [$inicio, $fim];
    }
}
