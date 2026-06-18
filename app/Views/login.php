<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
<?php 
include '../Core/conexao.php';
require_once '../Utils/csrf.php';

$erro = '';

// Garante token CSRF na sessão (para reaproveitar em todos os POST)
csrf_token();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../Controllers/AuthController.php'; 
    require_once '../Models/UsuarioModel.php';
    $controller = new AuthController();
    $resultado = $controller->login($pdo);
    if ($resultado !== true && !empty($resultado)) {
        $erro = $resultado;
    }
}
?>


<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Turismo Curuçá - Portal</title>
   <link rel="stylesheet" href="/public/css/conexao.css">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Login</h1>
      </div>
      <div class="btn-box">
         <a href="/" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>
      </div>
   </nav>

   <main>
      <section id="login-section">
         <?php if (!empty($erro)) echo "<p style='color:red'>$erro</p>"; ?>

         <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required><br><br>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required><br><br>
            <button type="submit">Entrar</button>
         </form>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>

</body>

<script src="/public/js/script.js"></script>

</html>
