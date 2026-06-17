<?php
// SIMPLIFICAÇÃO GERAL: Implementar um framework MVC como Laravel para melhor organização do código, usar bibliotecas de autenticação seguras, e ORM para abstrair consultas ao banco de dados.
require_once '../Core/conexao.php';
require_once '../Models/VideoModel.php';
class VideoController {
    private $model;

    public function __construct($conexao) {
        $this->model = new VideoModel($conexao);
    }

    // ERRO: Duplicação na captura de $search. Recebe como parâmetro mas redefine do POST. Solução: Usar apenas o parâmetro passado ou capturar apenas no método.
    public function buscarVideos($search) {
        return $this->model->buscarVideos($search);
    }

    public function buscarVideo($id) {
        return $this->model->buscarVideo($id);
    }

    public function criarVideo($dados) {
        $titulo = trim($dados['titulo'] ?? '');
        $descricao = trim($dados['descricao'] ?? '');
        $video = trim($dados['video'] ?? '');

        if (!$this->validarDadosVideo($titulo, $video)) {
            return false;
        }

        return $this->model->criarVideo($titulo, $descricao, $video);
    }

    public function atualizarVideo($id, $dados) {
        $titulo = trim($dados['titulo'] ?? '');
        $descricao = trim($dados['descricao'] ?? '');
        $video = trim($dados['video'] ?? '');

        if (!$this->validarDadosVideo($titulo, $video)) {
            return false;
        }

        return $this->model->atualizarVideo($id, $titulo, $descricao, $video);
    }

    public function excluirVideo($id) {
        return $this->model->excluirVideo($id);
    }

    private function validarUrlVideo($url) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $permitidas = ['mp4', 'webm', 'ogg', 'mov'];

        return in_array($ext, $permitidas, true);
    }

    private function validarDadosVideo($titulo, $video) {
        return $titulo !== '' && $video !== '' && $this->validarUrlVideo($video);
    }
}

?>
