<?php
class AuthController
{
   public function login($db)
   {
      $erro = '';

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
         $email = trim($_POST['email'] ?? '');
         $senha = trim($_POST['senha'] ?? '');

         if (empty($email)) {
            return "Preencha seu e-mail";
         }
         if (empty($senha)) {
            return "Preencha sua senha";
         }

         // Buscar usuário no banco
         $model = new UsuarioModel($db);
         $usuario = $model->buscarPorEmail($email);

         // Validar credenciais
         if (!$usuario) {
            return "E-mail não encontrado";
         }

         if (!password_verify($senha, $usuario['senha'])) {
            return "Senha incorreta";
         }

         // Tudo correto, fazer login
         $_SESSION['id'] = $usuario['id'];
         $_SESSION['nome'] = $usuario['nome'];
         header("Location: admin.php");
         exit();
      }
      return '';
   }
}
?>


