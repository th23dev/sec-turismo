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
}

?>