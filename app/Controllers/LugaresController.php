<?php
require_once '../Models/LugaresModel.php';
require_once '../Utils/ImageUpload.php';

class LugaresController
{
    private $model;
    private $tipo;

    public function __construct($conexao)
    {
        $this->model = new LugaresModel($conexao);
    }

    public function buscarLugares($tipo)
    {
        $search = isset($_POST['search']) ? $_POST['search'] : '';
        return $this->model->buscarLugares($search, $tipo);
    }

    public function buscarTodosOsLugares()
    {
        return $this->model->buscarTodosOsLugares();
    }

    public function buscarLugar($id)
    {
        return $this->model->buscarLugar($id);
    }

    public function criarLocal($dados, $arquivos = [])
    {
        require_once '../Utils/ImageUpload.php';
        
        $imagem_principal = '';
        
        // Processa arquivo de upload
        if (!empty($arquivos['arquivo_imagem']['name'])) {
            $uploaded_path = ImageUpload::upload($arquivos['arquivo_imagem']);
            if ($uploaded_path) {
                $imagem_principal = $uploaded_path;
            } else {
                return false; // Upload falhou
            }
        } 
        // Processa URL se arquivo não foi enviado
        else {
            $imagem_principal = $dados['imagem_principal_url'] ?? '';
            if (!empty($imagem_principal) && !ImageUpload::validateUrl($imagem_principal)) {
                return false; // URL inválida
            }
        }
        
        if (empty($imagem_principal)) {
            return false; // Nenhuma imagem fornecida
        }

        $nome = $dados['nome'] ?? '';
        $tipo = $dados['tipo'] ?? '';
        $numero = $dados['numero'] ?? '';
        $instagram = $dados['instagram'] ?? '';
        $linkInstagram = $dados['linkInstagram'] ?? '';
        $descricao = $dados['descricao'] ?? '';
        $possui_restaurante = ($dados['restaurante'] ?? '0') === '1' || ($dados['restaurante'] ?? 0) == 1 ? 1 : 0;

        $lugar_id = $this->model->criarLocal($imagem_principal, $nome, $tipo, $numero, $instagram, $linkInstagram, $descricao, $possui_restaurante);

        // Se criou com sucesso e tem mídias adicionais, salva elas
        if ($lugar_id && !empty($dados['midias'])) {
            $urls = array_filter(array_map('trim', explode("\n", $dados['midias'])));
            foreach ($urls as $url) {
                if (!empty($url)) {
                    $this->model->criarMidia($lugar_id, $url);
                }
            }
        }

        return $lugar_id;
    }

    public function excluirLugar($id){
        return $this->model->excluirLugar($id);
    }
    
    public function atualizarLocal($id, $imagem_principal = '', $nome, $tipo, $numero, $instagram, $linkInstagram, $descricao, $possui_restaurante, $arquivos = [])
    {
        require_once '../Utils/ImageUpload.php';
        
        // Se não foi fornecida nova imagem, mantém a anterior
        if (empty($imagem_principal)) {
            $imagem_principal_final = '';
        } else {
            // Processa arquivo de upload
            if (!empty($arquivos['arquivo_imagem']['name'])) {
                $uploaded_path = ImageUpload::upload($arquivos['arquivo_imagem']);
                if ($uploaded_path) {
                    $imagem_principal_final = $uploaded_path;
                    
                    // Deleta imagem anterior se for upload (não URL)
                    $anterior = $this->model->buscarLugar($id);
                    if ($anterior && strpos($anterior['imagem_principal'], 'uploads') !== false) {
                        ImageUpload::delete($anterior['imagem_principal']);
                    }
                } else {
                    return false; // Upload falhou
                }
            } 
            // Processa URL se arquivo não foi enviado
            else {
                $imagem_principal_final = $imagem_principal;
                if (!empty($imagem_principal_final) && !ImageUpload::validateUrl($imagem_principal_final)) {
                    return false; // URL inválida
                }
            }
        }
        
        $possui_restaurante = $possui_restaurante === '1' || $possui_restaurante == 1 ? 1 : 0;
        return $this->model->atualizarLocal($id, $imagem_principal_final, $nome, $tipo, $numero, $instagram, $linkInstagram, $descricao, $possui_restaurante);
    }

    //-------------- Midias --------------

    public function buscarMidias($id){
        return $this->model->buscarMidias($id);
    }

    public function criarMidia($lugar_id, $url){
        return $this->model->criarMidia($lugar_id, $url);
    }

    public function excluirMidia($midia_id){
        return $this->model->excluirMidia($midia_id);
    }

    public function excluirMidiasPorLugar($lugar_id){
        return $this->model->excluirMidiasPorLugar($lugar_id);
    }
}
