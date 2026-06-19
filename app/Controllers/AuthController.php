<?php
require_once __DIR__ . '/../Utils/csrf.php';
require_once __DIR__ . '/../Utils/url.php';

class AuthController
{
   private const MAX_LOGIN_ATTEMPTS = 5;
   private const LOGIN_LOCK_SECONDS = 900;
   private const LOGIN_ERROR_MESSAGE = 'E-mail ou senha inválidos.';

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

      if ($this->isLoginLocked()) {
         return 'Muitas tentativas de login. Aguarde alguns minutos e tente novamente.';
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
         $this->registerFailedLogin();
         return self::LOGIN_ERROR_MESSAGE;
      }

      if (!password_verify($senha, $usuario['senha'])) {
         $this->registerFailedLogin();
         return self::LOGIN_ERROR_MESSAGE;
      }

      session_regenerate_id(true);
      unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);

      $_SESSION['id'] = $usuario['id'];
      $_SESSION['nome'] = $usuario['nome'];
      $_SESSION['last_activity'] = time();

      header("Location: " . redirect_url('admin'));
      exit();
   }

   private function isLoginLocked(): bool
   {
      $lockedUntil = (int) ($_SESSION['login_locked_until'] ?? 0);
      return $lockedUntil > time();
   }

   private function registerFailedLogin(): void
   {
      $attempts = (int) ($_SESSION['login_attempts'] ?? 0) + 1;
      $_SESSION['login_attempts'] = $attempts;

      if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
         $_SESSION['login_locked_until'] = time() + self::LOGIN_LOCK_SECONDS;
      }
   }
}
