<?php
require_once __DIR__ . '/../Utils/url.php';
start_url_rewriter();

$paginas = [
   'manguezais' => ['titulo' => 'Manguezais', 'icone' => 'fa-seedling'],
   'gastronomia' => ['titulo' => 'Gastronomia', 'icone' => 'fa-utensils'],
   'cultura-popular' => ['titulo' => 'Cultura Popular', 'icone' => 'fa-drum'],
   'trilhas' => ['titulo' => 'Trilhas', 'icone' => 'fa-route'],
];

$slug = $_GET['pagina'] ?? '';
$pagina = $paginas[$slug] ?? ['titulo' => 'Nova experi&ecirc;ncia', 'icone' => 'fa-compass'];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?= $pagina['titulo']; ?> - Em breve</title>
   <link rel="stylesheet" href="<?= asset_url('css/conexao.css'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/em_breve.css'); ?>">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1><?= $pagina['titulo']; ?></h1>
      </div>
      <div class="btn-box">
         <a href="<?= view_url('menu.php'); ?>" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>
         <a href="<?= app_url('/'); ?>" class="btn-voltar">
            In&iacute;cio <i class="fas fa-house"></i>
         </a>
      </div>
   </nav>

   <main class="coming-soon-page">
      <section class="coming-soon" aria-labelledby="coming-soon-title">
         <div class="coming-soon__icon">
            <i class="fas <?= $pagina['icone']; ?>"></i>
         </div>
         <span class="coming-soon__eyebrow">Em breve</span>
         <h2 id="coming-soon-title"><?= $pagina['titulo']; ?></h2>
         <p>Essa &aacute;rea ainda est&aacute; sendo preparada para receber conte&uacute;dos, roteiros e informa&ccedil;&otilde;es tur&iacute;sticas.</p>
         <a href="<?= view_url('menu.php'); ?>" class="coming-soon__button">
            Voltar para o menu
         </a>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>
</body>
<script src="<?= asset_url('js/script.js'); ?>"></script>

</html>
