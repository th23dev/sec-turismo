<?php
require_once __DIR__ . '/../Utils/url.php';
start_url_rewriter();

include('../Core/conexao.php');
include('../Controllers/protect.php');
require_once('../Controllers/ConteudoTuristicoController.php');
require_once('../Utils/csrf.php');

$controller = new ConteudoTuristicoController($pdo);
$restauranteId = intval($_GET['restaurante_id'] ?? 0);
$pratoId = intval($_GET['id'] ?? 0);
$restaurante = $restauranteId > 0 ? $controller->buscar('gastronomia', $restauranteId) : null;
$prato = ($restaurante && $pratoId > 0) ? $controller->buscarPrato($restauranteId, $pratoId) : null;
$erro = '';

$csrfToken = csrf_token();
send_security_headers();

if (!$restaurante || !$prato) {
   http_response_code(404);
   echo 'Prato nao encontrado.';
   exit;
}

$pratoTitulo = trim((string) ($prato['nome'] ?? ''));
if ($pratoTitulo === '') {
   $pratoTitulo = 'Prato sem nome';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   if (!csrf_validate($_POST['csrf_token'] ?? null)) {
      $erro = 'Token CSRF invalido.';
   } elseif ($controller->excluirPrato($restauranteId, $pratoId)) {
      header('Location: ' . redirect_url('editar_conteudo') . '?categoria=gastronomia&id=' . $restauranteId);
      exit;
   } else {
      $erro = 'Erro ao excluir o prato.';
   }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Excluir Prato - Turismo Curuca</title>
   <link rel="icon" type="image/webp" href="<?= asset_url('imgs/logos-bg/logo-sec-turismo.webp'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/conexao.css'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/editar.css'); ?>">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Excluir Prato</h1>
      </div>
      <div class="btn-box">
         <a href="<?= redirect_url('editar_conteudo'); ?>?categoria=gastronomia&id=<?= intval($restauranteId); ?>" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>
      </div>
   </nav>

   <main>
      <section id="section-editar">
         <?php if ($erro): ?>
            <div class="alert alert-erro"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro); ?></div>
         <?php endif; ?>

         <form action="" method="post" class="editar-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken); ?>">
            <div class="form-section">
               <h3><i class="fas fa-triangle-exclamation"></i> Confirmar exclusao</h3>
               <p>Tem certeza que deseja excluir <strong><?= htmlspecialchars($pratoTitulo); ?></strong> de <strong><?= htmlspecialchars($restaurante['nome'] ?? 'Restaurante'); ?></strong>?</p>
            </div>
            <div class="form-actions">
               <button type="submit" class="btn-cancelar btn-excluir"><i class="fas fa-trash"></i> Excluir</button>
               <a href="<?= redirect_url('editar_conteudo'); ?>?categoria=gastronomia&id=<?= intval($restauranteId); ?>" class="btn-salvar"><i class="fas fa-times"></i> Cancelar</a>
            </div>
         </form>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>
</body>

</html>
