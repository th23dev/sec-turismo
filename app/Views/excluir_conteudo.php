<?php
require_once __DIR__ . '/../Utils/url.php';
start_url_rewriter();

include('../Core/conexao.php');
include('../Controllers/protect.php');
require_once('../Controllers/ConteudoTuristicoController.php');
require_once('../Utils/csrf.php');

$controller = new ConteudoTuristicoController($pdo);
$categoria = (string) ($_GET['categoria'] ?? 'gastronomia');
$id = intval($_GET['id'] ?? 0);
$info = $controller->categoriaInfo($categoria);
$item = $id > 0 ? $controller->buscar($categoria, $id) : null;
$erro = '';

$csrfToken = csrf_token();
send_security_headers();

if (!$info || !$item) {
   http_response_code(404);
   echo 'Cadastro nao encontrado.';
   exit;
}

$itemTitulo = trim((string) ($item['nome'] ?? $item['titulo'] ?? ''));
if ($itemTitulo === '') {
   $itemTitulo = 'Cadastro sem titulo';
}

$categoriaTitulo = (string) ($info['titulo'] ?? 'conteudos turisticos');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   if (!csrf_validate($_POST['csrf_token'] ?? null)) {
      $erro = 'Token CSRF invalido.';
   } elseif ($controller->excluir($categoria, $id)) {
      header('Location: ' . redirect_url('admin'));
      exit;
   } else {
      $erro = 'Erro ao excluir o cadastro.';
   }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Excluir <?= htmlspecialchars($itemTitulo, ENT_QUOTES, 'UTF-8'); ?> - Turismo Curuca</title>
   <link rel="icon" type="image/webp" href="<?= asset_url('imgs/logos-bg/logo-sec-turismo.webp'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/conexao.css'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/editar.css'); ?>">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Excluir <?= htmlspecialchars($itemTitulo, ENT_QUOTES, 'UTF-8'); ?></h1>
      </div>
   </nav>

   <main>
      <section id="section-editar">
         <?php if ($erro): ?>
            <div class="alert alert-erro"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
         <?php endif; ?>
         <form action="" method="post" class="editar-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="form-section">
               <h3><i class="fas fa-triangle-exclamation"></i> Confirmar exclusao</h3>
               <p>Tem certeza que deseja excluir <strong><?= htmlspecialchars($itemTitulo, ENT_QUOTES, 'UTF-8'); ?></strong> de <?= htmlspecialchars($categoriaTitulo, ENT_QUOTES, 'UTF-8'); ?>?</p>
            </div>
            <div class="form-actions">
               <button type="submit" class="btn-cancelar btn-excluir"><i class="fas fa-trash"></i> Excluir</button>
               <a href="<?= redirect_url('admin'); ?>" class="btn-salvar"><i class="fas fa-times"></i> Cancelar</a>
            </div>
         </form>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>
</body>

</html>
