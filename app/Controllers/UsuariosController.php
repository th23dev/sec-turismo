<?php
require_once __DIR__ . '/../Models/UsuarioModel.php';
require_once __DIR__ . '/../Utils/csrf.php';

class UsuariosController
{
    private $model;

    public function __construct($conexao)
    {
        $this->model = new UsuarioModel($conexao);
    }

    public function criar(array $dados): array
    {
        $nome = trim($dados['nome'] ?? '');
        $email = trim($dados['email'] ?? '');
        $senha = (string)($dados['senha'] ?? '');
        $senha_confirmacao = (string)($dados['senha_confirmacao'] ?? '');

        if (!csrf_validate($dados['csrf_token'] ?? null)) {
            return ['ok' => false, 'message' => 'Token CSRF inválido.'];
        }

        if ($nome === '') {
            return ['ok' => false, 'message' => 'Informe o nome.'];
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'message' => 'Informe um e-mail válido.'];
        }

        if ($senha === '' || strlen($senha) < 6) {
            return ['ok' => false, 'message' => 'A senha deve ter pelo menos 6 caracteres.'];
        }

        if ($senha !== $senha_confirmacao) {
            return ['ok' => false, 'message' => 'As senhas não conferem.'];
        }

        // Valida se já existe e-mail
        $existente = $this->model->buscarPorEmail($email);
        if ($existente) {
            return ['ok' => false, 'message' => 'Já existe um usuário com este e-mail.'];
        }

        $ok = $this->model->criarUsuario($nome, $email, $senha);
        if (!$ok) {
            return ['ok' => false, 'message' => 'Não foi possível criar o usuário.'];
        }

        return ['ok' => true, 'message' => 'Usuário criado com sucesso!'];
    }
}

