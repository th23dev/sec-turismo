<?php
require_once __DIR__ . '/../../Utils/url.php';
start_url_rewriter();

include __DIR__ . '/../../Core/conexao.php';
require_once __DIR__ . '/../../Controllers/ConteudoTuristicoController.php';

$categoria = $categoria ?? '';
$controller = new ConteudoTuristicoController($pdo);
$info = $controller->categoriaInfo($categoria) ?? ['titulo' => 'Experiências', 'icone' => 'fa-map-location-dot', 'title_column' => 'titulo'];
$search = $_POST['search'] ?? $_GET['search'] ?? '';
$itens = $controller->buscarTodos($categoria, $search);
$prefixo = str_replace('_', '-', $categoria);
$modalClass = 'modal-box modal-box--' . $prefixo;

function conteudo_titulo(array $item, array $info, string $categoria): string
{
   $column = $info['title_column'] ?? null;
   if ($column && !empty($item[$column])) {
      return (string) $item[$column];
   }

   return $categoria === 'cultura_popular' ? 'Cultura Popular' : 'Experiência';
}

function primeira_imagem(array $item): string
{
   if (!empty($item['imagem_principal'])) {
      return $item['imagem_principal'];
   }

   if (!empty($item['fotos'][0]['url'])) {
      return $item['fotos'][0]['url'];
   }

   if (!empty($item['pratos'][0]['foto'])) {
      return $item['pratos'][0]['foto'];
   }

   return '';
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Turismo Curu&ccedil;&aacute; - <?= htmlspecialchars($info['titulo']); ?></title>
   <link rel="icon" type="image/webp" href="<?= asset_url('imgs/logos-bg/logo-sec-turismo.webp'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/conexao.css'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/catalogo.css'); ?>">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1><?= htmlspecialchars($info['titulo']); ?></h1>
      </div>
      <div class="btn-box">
         <a href="<?= view_url('menu.php'); ?>" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>

         <form action="" method="post">
            <input type="search" name="search" id="search-input" placeholder="Buscar..." value="<?= htmlspecialchars($search); ?>">
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
            <?php if (!empty($itens)): ?>
               <?php foreach ($itens as $item): ?>
                  <?php $imagem = primeira_imagem($item); ?>
                  <div class="card" onclick="openModal('<?= htmlspecialchars($prefixo); ?>-<?= intval($item['id']); ?>')">
                     <?php if ($imagem !== ''): ?>
                        <img src="<?= htmlspecialchars($imagem); ?>" alt="<?= htmlspecialchars(conteudo_titulo($item, $info, $categoria)); ?>">
                     <?php else: ?>
                        <div class="card-image-placeholder"><i class="fas <?= htmlspecialchars($info['icone']); ?>"></i></div>
                     <?php endif; ?>
                     <h3><?= htmlspecialchars(conteudo_titulo($item, $info, $categoria)); ?></h3>
                  </div>
               <?php endforeach; ?>
            <?php else: ?>
               <p style="padding:20px;">Nenhum cadastro encontrado para "<strong><?= htmlspecialchars($search ?: 'nenhum termo'); ?></strong>".</p>
            <?php endif; ?>
         </div>
      </section>
   </main>

   <?php foreach ($itens as $item): ?>
      <div id="modal-<?= htmlspecialchars($prefixo); ?>-<?= intval($item['id']); ?>" class="modal">
         <div class="<?= htmlspecialchars($modalClass); ?>">
            <span class="close" onclick="closeModal('<?= htmlspecialchars($prefixo); ?>-<?= intval($item['id']); ?>')">&times;</span>
            <div class="image-carousel">
               <?php if ($categoria === 'trilha' && !empty($item['trajeto_maps_url']) && is_safe_google_maps_url($item['trajeto_maps_url'])): ?>
                  <iframe class="catalog-map catalog-map-main" src="<?= htmlspecialchars($item['trajeto_maps_url']); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
               <?php else: ?>
                  <?php $imagens = array_values(array_unique(array_filter(array_merge([primeira_imagem($item)], array_column($item['fotos'] ?? [], 'url'))))); ?>
                  <div class="carousel-images">
                     <?php if (!empty($imagens)): ?>
                        <?php foreach ($imagens as $imagem): ?>
                           <div class="carousel-image" style="background-image: url('<?= htmlspecialchars($imagem); ?>');"></div>
                        <?php endforeach; ?>
                     <?php else: ?>
                        <div class="carousel-image carousel-image-empty"><i class="fas <?= htmlspecialchars($info['icone']); ?>"></i></div>
                     <?php endif; ?>
                  </div>
                  <?php if (count($imagens) > 1): ?>
                     <button class="carousel-btn prev" type="button" onclick="prevImage('<?= htmlspecialchars($prefixo); ?>-<?= intval($item['id']); ?>')" aria-label="Foto anterior">
                        <i class="fas fa-chevron-left"></i>
                     </button>
                     <button class="carousel-btn next" type="button" onclick="nextImage('<?= htmlspecialchars($prefixo); ?>-<?= intval($item['id']); ?>')" aria-label="Proxima foto">
                        <i class="fas fa-chevron-right"></i>
                     </button>
                     <div class="carousel-indicators"></div>
                  <?php endif; ?>
               <?php endif; ?>
            </div>
            <div class="text-box">
               <?php if ($categoria === 'gastronomia'): ?>
                  <span class="modal-eyebrow">Restaurante</span>
               <?php endif; ?>
               <h2><?= htmlspecialchars(conteudo_titulo($item, $info, $categoria)); ?></h2>
               <p><?= nl2br(htmlspecialchars($item['descricao'] ?? '')); ?></p>

               <?php if ($categoria === 'gastronomia' && !empty($item['horario_funcionamento'])): ?>
                  <p><strong>Horário:</strong> <?= nl2br(htmlspecialchars($item['horario_funcionamento'])); ?></p>
               <?php endif; ?>

               <div class="info-tags">
                  <?php if (!empty($item['numero'])): ?>
                     <span class="tag"><i class="fas fa-phone"></i><?= htmlspecialchars($item['numero']); ?></span>
                  <?php endif; ?>
                  <?php if (!empty($item['instagram']) && is_safe_http_url($item['instagram'])): ?>
                     <a class="tag insta" href="<?= htmlspecialchars($item['instagram']); ?>" target="_blank" rel="noopener">
                        <i class="fab fa-instagram"></i>Instagram
                     </a>
                  <?php endif; ?>
                  <?php if (!empty($item['site_url']) && is_safe_http_url($item['site_url'])): ?>
                     <a class="tag map-link" href="<?= htmlspecialchars($item['site_url']); ?>" target="_blank" rel="noopener">
                        <i class="fas fa-up-right-from-square"></i>Site
                     </a>
                  <?php endif; ?>
                  <?php if (!empty($item['google_maps_url']) && is_safe_google_maps_url($item['google_maps_url'])): ?>
                     <a class="tag map-link" href="<?= htmlspecialchars($item['google_maps_url']); ?>" target="_blank" rel="noopener">
                        <i class="fas fa-map-location-dot"></i>Localização
                     </a>
                  <?php endif; ?>
               </div>

               <?php if (in_array($categoria, ['gastronomia', 'manguezais'], true) && !empty($item['google_maps_url']) && str_contains($item['google_maps_url'], '/maps/embed') && is_safe_google_maps_url($item['google_maps_url'])): ?>
                  <iframe class="catalog-map" src="<?= htmlspecialchars($item['google_maps_url']); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
               <?php endif; ?>

               <?php if ($categoria === 'trilha' && !empty($item['fotos'])): ?>
                  <h3 class="modal-section-title"><i class="fas fa-images"></i> Fotos da trilha</h3>
                  <div class="modal-secondary-gallery">
                     <?php foreach ($item['fotos'] as $foto): ?>
                        <?php if (!empty($foto['url'])): ?>
                           <img src="<?= htmlspecialchars($foto['url']); ?>" alt="Foto de <?= htmlspecialchars(conteudo_titulo($item, $info, $categoria)); ?>">
                        <?php endif; ?>
                     <?php endforeach; ?>
                  </div>
               <?php endif; ?>

               <?php if ($categoria === 'gastronomia' && !empty($item['pratos'])): ?>
                  <h3 class="modal-section-title"><i class="fas fa-bowl-food"></i> Pratos</h3>
                  <div class="dish-list">
                     <?php foreach ($item['pratos'] as $prato): ?>
                        <article class="dish-item">
                           <?php if (!empty($prato['foto'])): ?>
                              <img src="<?= htmlspecialchars($prato['foto']); ?>" alt="<?= htmlspecialchars($prato['nome'] ?? 'Prato'); ?>">
                           <?php endif; ?>
                           <div>
                              <h3><?= htmlspecialchars($prato['nome'] ?? 'Prato'); ?></h3>
                              <p><?= htmlspecialchars($prato['descricao'] ?? ''); ?></p>
                           </div>
                        </article>
                     <?php endforeach; ?>
                  </div>
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
