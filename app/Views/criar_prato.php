<?php
require_once __DIR__ . '/../Utils/url.php';
start_url_rewriter();

include('../Core/conexao.php');
include('../Controllers/protect.php');
require_once('../Controllers/ConteudoTuristicoController.php');
require_once('../Utils/csrf.php');

$controller = new ConteudoTuristicoController($pdo);
$restauranteId = intval($_GET['restaurante_id'] ?? 0);
$restaurante = $restauranteId > 0 ? $controller->buscar('gastronomia', $restauranteId) : null;
$erro = '';

$csrfToken = csrf_token();
send_security_headers();

if (!$restaurante) {
   echo 'Restaurante nao encontrado.';
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   if (!csrf_validate($_POST['csrf_token'] ?? null)) {
      $erro = 'Token CSRF invalido.';
   } elseif ($controller->criarPrato($restauranteId, $_POST, $_FILES)) {
      header('Location: ' . redirect_url('editar_conteudo') . '?categoria=gastronomia&id=' . $restauranteId);
      exit;
   } else {
      $erro = 'Erro ao criar o prato. Informe pelo menos nome, descricao ou foto.';
   }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Criar Prato - Turismo Curuca</title>
   <link rel="icon" type="image/webp" href="<?= asset_url('imgs/logos-bg/logo-sec-turismo.webp'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/conexao.css'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/editar.css'); ?>">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Criar Prato</h1>
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
               <span class="crud-eyebrow">Novo prato</span>
               <h2><?= htmlspecialchars($restaurante['nome'] ?? 'Restaurante'); ?></h2>
               <p>Cadastre um prato por vez para manter o restaurante organizado e facilitar a edicao depois.</p>
            </div>
            <a href="<?= redirect_url('editar_conteudo'); ?>?categoria=gastronomia&id=<?= intval($restauranteId); ?>" class="crud-intro__link">
               <i class="fas fa-utensils"></i> Restaurante
            </a>
         </div>

         <?php if ($erro): ?>
            <div class="alert alert-erro"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro); ?></div>
         <?php endif; ?>

         <form action="" method="post" class="editar-form" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken); ?>">

            <div class="form-section">
               <h3><i class="fas fa-bowl-food"></i> Dados do prato</h3>
               <div class="form-row">
                  <div class="form-group">
                     <label for="prato_nome">Nome do prato</label>
                     <input type="text" name="prato_nome" id="prato_nome" placeholder="Ex: Peixe frito com acompanhamentos">
                  </div>
                  <div class="form-group">
                     <label for="prato_foto_arquivo">Foto do prato</label>
                     <input type="file" name="prato_foto_arquivo" id="prato_foto_arquivo" accept="image/jpeg,image/png,image/webp,image/gif">
                  </div>
               </div>
               <div class="form-group">
                  <label for="prato_foto_url">Ou URL da foto</label>
                  <input type="text" name="prato_foto_url" id="prato_foto_url" placeholder="https://exemplo.com/prato.jpg">
               </div>
               <div class="form-group">
                  <label for="prato_descricao">Descricao</label>
                  <textarea name="prato_descricao" id="prato_descricao" rows="4" placeholder="Ingredientes, destaque ou breve descricao."></textarea>
               </div>
            </div>

            <div class="form-actions sticky-actions">
               <a href="<?= redirect_url('editar_conteudo'); ?>?categoria=gastronomia&id=<?= intval($restauranteId); ?>" class="btn-cancelar">
                  <i class="fas fa-times"></i> Cancelar
               </a>
               <button type="submit" class="btn-salvar">
                  <i class="fas fa-plus"></i> Criar prato
               </button>
            </div>
         </form>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>
   <script src="<?= asset_url('js/script.js'); ?>"></script>
</body>

</html>
