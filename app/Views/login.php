<?php 
// SIMPLIFICAÇÃO GERAL: Separar lógica de autenticação da view e passar mensagens de erro como variável.
include '../Core/conexao.php';

$erro = '';

// Garantir que a sessão está iniciada e token CSRF existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $erro = 'Token CSRF inválido.';
    } else {
        require_once '../Controllers/AuthController.php'; 
        require_once '../Models/UsuarioModel.php';
        $controller = new AuthController();
        $resultado = $controller->login($pdo);
        if ($resultado !== true && !empty($resultado)) {
            $erro = $resultado;
        }
    }
}
?>

<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Turismo Curuçá - Portal</title>
   <link rel="stylesheet" href="../../public/css/conexao.css">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Login</h1>
      </div>
      <div class="btn-box">
         <a href="../../public/index.php" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>
      </div>
   </nav>

   <main>
      <section id="login-section">
         <!-- ERRO: $erro nunca é definido no escopo da view. Solução: o controller deve definir $erro antes de renderizar ou a variável deve ser inicializada. -->
         <?php if (!empty($erro)) echo "<p style='color:red'>$erro</p>"; ?>

         <!-- ERRO: formulário de login não possui token CSRF. Solução: gerar token CSRF na sessão e validar ao receber POST. -->
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

<script src="../../public/js/script.js"></script>

</html>
