<?php
// SIMPLIFICAÇÃO GERAL: Implementar um framework MVC como Laravel para melhor organização do código, usar bibliotecas de autenticação seguras, e ORM para abstrair consultas ao banco de dados.
// ERRO: Senha em texto plano. Risco: Qualquer pessoa com acesso ao banco vê as senhas. Solução: Usar password_hash() no cadastro e password_verify() no login.
class UsuarioModel {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: false;
    }

    public function criarUsuario($nome, $email, $senha) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nome, $email, $senhaHash]);
    }
}
?>
