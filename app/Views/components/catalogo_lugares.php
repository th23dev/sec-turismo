<?php
require_once __DIR__ . '/../../Utils/url.php';
start_url_rewriter();

include __DIR__ . '/../../Core/conexao.php';
require_once __DIR__ . '/../../Controllers/LugaresController.php';

$catalogoTipo = $catalogoTipo ?? '';
$catalogoTitulo = $catalogoTitulo ?? 'Atrativos';
$catalogoBusca = $catalogoBusca ?? 'Buscar atrativos...';
$catalogoIcone = $catalogoIcone ?? 'fa-map-location-dot';
$catalogoModalPrefixo = $catalogoModalPrefixo ?? preg_replace('/[^a-z0-9_-]/i', '-', $catalogoTipo);

$controller = new LugaresController($pdo);
$search = $_POST['search'] ?? $_GET['search'] ?? '';
$lugares = $controller->buscarLugares($catalogoTipo);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Turismo Curu&ccedil;&aacute; - <?= htmlspecialchars($catalogoTitulo); ?></title>
   <link rel="icon" type="image/webp" href="<?= asset_url('imgs/logos-bg/logo-sec-turismo.webp'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/conexao.css'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/catalogo.css'); ?>">
</head>

<body>

   <nav class="back-nav">
      <div class="text-box">
         <h1><?= htmlspecialchars($catalogoTitulo); ?></h1>
      </div>
      <div class="btn-box">
         <a href="<?= view_url('menu.php'); ?>" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>

         <form action="" method="post">
            <input type="search" name="search" id="search-input" placeholder="<?= htmlspecialchars($catalogoBusca); ?>" value="<?= htmlspecialchars($search ?? ''); ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
         </form>

         <a href="<?= app_url('/'); ?>" class="btn-voltar">
            In&iacute;cio <i class="fas fa-house"></i>
         </a>
      </div>
   </nav>

   <main>
      <section class="catalogo">
         <div class="cards-grid">
            <?php if (count($lugares) > 0): ?>
               <?php foreach ($lugares as $lugar): ?>
                  <div class="card" onclick="openModal('<?= htmlspecialchars($catalogoModalPrefixo); ?>-<?php echo intval($lugar['id']); ?>')">
                     <?php if (!empty($lugar['imagem_principal'])): ?>
                        <img src="<?= htmlspecialchars($lugar['imagem_principal']); ?>" alt="<?= htmlspecialchars($lugar['nome']); ?>">
                     <?php else: ?>
                        <div class="card-image-placeholder"><i class="fas <?= htmlspecialchars($catalogoIcone); ?>"></i></div>
                     <?php endif; ?>
                     <h3><?= htmlspecialchars($lugar['nome']); ?></h3>
                  </div>
               <?php endforeach; ?>
            <?php else: ?>
               <p style="padding:20px;">Nenhum cadastro encontrado para "<strong><?= htmlspecialchars($search ?: 'nenhum termo'); ?></strong>".</p>
            <?php endif; ?>
         </div>
      </section>
   </main>

   <?php foreach ($lugares as $lugar): ?>
   <div id="modal-<?= htmlspecialchars($catalogoModalPrefixo); ?>-<?= intval($lugar['id']); ?>" class="modal">
      <div class="modal-box">
         <span class="close" onclick="closeModal('<?= htmlspecialchars($catalogoModalPrefixo); ?>-<?= intval($lugar['id']); ?>')">&times;</span>
         <div class="image-carousel">
            <div class="carousel-images">
               <?php if (!empty($lugar['imagem_principal'])): ?>
                  <div class="carousel-image" style="background-image: url('<?= htmlspecialchars($lugar['imagem_principal']); ?>');"></div>
               <?php else: ?>
                  <div class="carousel-image carousel-image-empty"><i class="fas <?= htmlspecialchars($catalogoIcone); ?>"></i></div>
               <?php endif; ?>
               <?php foreach ($lugar['url'] as $imagem): ?>
                  <div class="carousel-image" style="background-image: url('<?= htmlspecialchars($imagem); ?>');"></div>
               <?php endforeach; ?>
            </div>
            <button class="carousel-btn prev" onclick="prevImage('<?= htmlspecialchars($catalogoModalPrefixo); ?>-<?= intval($lugar['id']); ?>')"> < </button>
            <button class="carousel-btn next" onclick="nextImage('<?= htmlspecialchars($catalogoModalPrefixo); ?>-<?= intval($lugar['id']); ?>')"> > </button>
            <div class="carousel-indicators"></div>
         </div>
         <div class="text-box">
            <h2><?= htmlspecialchars($lugar['nome']); ?></h2>
            <p><?= htmlspecialchars($lugar['descricao']); ?></p>
            <div class="info-tags">
               <?php if (!empty($lugar['numero'])): ?>
                  <span class="tag"><i class="fas fa-phone"></i><?= htmlspecialchars($lugar['numero']); ?></span>
               <?php endif; ?>
               <?php if (!empty($lugar['instagram']) && is_safe_http_url($lugar['linkInstagram'] ?? '')): ?>
                  <a class="tag insta" href="<?= htmlspecialchars($lugar['linkInstagram']); ?>/" target="_blank" rel="noopener">
                     <i class="fab fa-instagram"></i><?= htmlspecialchars($lugar['instagram']); ?>
                  </a>
               <?php endif; ?>
               <?php if ($lugar['possui_restaurante']): ?>
                  <span class="tag"><i class="fas fa-utensils"></i>Restaurante</span>
               <?php endif; ?>
               <?php if (!empty($lugar['google_maps_url']) && is_safe_google_maps_url($lugar['google_maps_url'])): ?>
                  <a class="tag map-link" href="<?= htmlspecialchars($lugar['google_maps_url']); ?>" target="_blank" rel="noopener">
                     <i class="fas fa-map-location-dot"></i>Mapa
                  </a>
               <?php endif; ?>
            </div>
            <?php if (!empty($lugar['google_maps_url']) && str_contains($lugar['google_maps_url'], '/maps/embed') && is_safe_google_maps_url($lugar['google_maps_url'])): ?>
               <iframe class="catalog-map" src="<?= htmlspecialchars($lugar['google_maps_url']); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            <?php endif; ?>
         </div>
      </div>
   </div>
   <?php endforeach; ?>

   <?php include __DIR__ . '/footer.php'; ?>

</body>
<script src="<?= asset_url('js/script.js'); ?>"></script>
<script src="<?= asset_url('js/catalogo.js'); ?>"></script>

</html>
