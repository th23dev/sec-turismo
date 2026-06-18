<?php
require_once __DIR__ . '/../Utils/csrf.php';
require_once __DIR__ . '/../Utils/url.php';

class AuthController
{
   public function login($db)
   {
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
         return '';
      }

      $email = trim($_POST['email'] ?? '');
      $senha = trim($_POST['senha'] ?? '');
      $csrfToken = $_POST['csrf_token'] ?? null;

      if (!csrf_validate($csrfToken)) {
         return 'Token CSRF inválido.';
      }

      if (empty($email)) {
         return "Preencha seu e-mail";
      }
      if (empty($senha)) {
         return "Preencha sua senha";
      }

      $model = new UsuarioModel($db);
      $usuario = $model->buscarPorEmail($email);

      if (!$usuario) {
         return "E-mail não encontrado";
      }

      if (!password_verify($senha, $usuario['senha'])) {
         return "Senha incorreta";
      }

      // Tudo correto, fazer login
      $_SESSION['id'] = $usuario['id'];
      $_SESSION['nome'] = $usuario['nome'];

      header("Location: " . redirect_url('admin'));
      exit();
   }
}
