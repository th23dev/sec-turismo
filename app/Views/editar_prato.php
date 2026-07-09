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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   if (!csrf_validate($_POST['csrf_token'] ?? null)) {
      $erro = 'Token CSRF invalido.';
   } elseif ($controller->atualizarPrato($restauranteId, $pratoId, $_POST, $_FILES)) {
      header('Location: ' . redirect_url('editar_conteudo') . '?categoria=gastronomia&id=' . $restauranteId);
      exit;
   } else {
      $erro = 'Erro ao atualizar o prato. Verifique a imagem ou informe pelo menos nome, descricao ou foto.';
   }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Editar Prato - Turismo Curuca</title>
   <link rel="icon" type="image/webp" href="<?= asset_url('imgs/logos-bg/logo-sec-turismo.webp'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/conexao.css'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/editar.css'); ?>">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Editar Prato</h1>
      </div>
      <div class="btn-box">
         <a href="<?= redirect_url('editar_conteudo'); ?>?categoria=gastronomia&id=<?= intval($restauranteId); ?>" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>
      </div>
   </nav>

   <main>
      <section id="section-editar">
         <div class="crud-intro">
            <div>
               <span class="crud-eyebrow">Editando prato</span>
               <h2><?= htmlspecialchars($restaurante['nome'] ?? 'Restaurante'); ?></h2>
               <p>Atualize nome, descricao e foto do prato cadastrado neste restaurante.</p>
            </div>
            <a href="<?= redirect_url('excluir_prato'); ?>?restaurante_id=<?= intval($restauranteId); ?>&id=<?= intval($pratoId); ?>" class="crud-intro__link">
               <i class="fas fa-trash"></i> Excluir
            </a>
         </div>

         <?php if ($erro): ?>
            <div class="alert alert-erro"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro); ?></div>
         <?php endif; ?>

         <form action="" method="post" class="editar-form" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken); ?>">

            <div class="form-section">
               <h3><i class="fas fa-bowl-food"></i> Dados do prato</h3>
               <?php if (!empty($prato['foto'])): ?>
                  <div class="image-preview-box"><img class="preview-visible" src="<?= htmlspecialchars($prato['foto']); ?>" alt="Foto atual do prato"></div>
               <?php endif; ?>
               <div class="form-row">
                  <div class="form-group">
                     <label for="prato_nome">Nome do prato</label>
                     <input type="text" name="prato_nome" id="prato_nome" value="<?= htmlspecialchars($prato['nome'] ?? ''); ?>" placeholder="Ex: Peixe frito com acompanhamentos">
                  </div>
                  <div class="form-group">
                     <label for="prato_foto_arquivo">Trocar foto do prato</label>
                     <input type="file" name="prato_foto_arquivo" id="prato_foto_arquivo" accept="image/jpeg,image/png,image/webp,image/gif">
                  </div>
               </div>
               <div class="form-group">
                  <label for="prato_foto_url">URL da foto</label>
                  <input type="text" name="prato_foto_url" id="prato_foto_url" value="<?= htmlspecialchars($prato['foto'] ?? ''); ?>" placeholder="https://exemplo.com/prato.jpg">
                  <small class="field-hint">Apague este campo e salve caso queira deixar o prato sem foto.</small>
               </div>
               <div class="form-group">
                  <label for="prato_descricao">Descricao</label>
                  <textarea name="prato_descricao" id="prato_descricao" rows="4" placeholder="Ingredientes, destaque ou breve descricao."><?= htmlspecialchars($prato['descricao'] ?? ''); ?></textarea>
               </div>
            </div>

            <div class="form-actions sticky-actions">
               <a href="<?= redirect_url('editar_conteudo'); ?>?categoria=gastronomia&id=<?= intval($restauranteId); ?>" class="btn-cancelar">
                  <i class="fas fa-times"></i> Cancelar
               </a>
               <button type="submit" class="btn-salvar">
                  <i class="fas fa-save"></i> Salvar alteracoes
               </button>
            </div>
         </form>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>
   <script src="<?= asset_url('js/script.js'); ?>"></script>
</body>

</html>
