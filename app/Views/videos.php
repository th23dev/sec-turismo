<?php 
// SIMPLIFICAÇÃO GERAL: Manter a view de vídeos leve e mover qualquer validação de busca para o controller.
include '../Core/conexao.php';
require_once '../Controllers/VideoController.php'; 
$controller = new VideoController($pdo);
$search = $_POST['search'] ?? $_GET['search'] ?? '';

$videos = $controller->buscarVideos($search);
?> 

<!DOCTYPE html>
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
         <h1>Vídeos</h1>
      </div>
      <div class="btn-box">
         <a href="menu.php" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>

         <form action="" method="post">
            <input type="search" name="search" id="search-input" placeholder="Buscar vídeos..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
         </form>

         <a href="../../public/index.php" class="btn-voltar">
            Início <i class="fas fa-house"></i>
         </a>
      </div>
   </nav>

   <main>
      <section class="video-carousel-section">
         <div class="carousel-container">

            <?php if (count($videos) > 3): ?>
               <button class="carousel-btn prev" id="prevBtn">
                  <i class="fas fa-chevron-left"></i>
               </button>
            <?php endif; ?>

            <div class="carousel-track-container">
               <ul class="carousel-track">

                  <?php if (count($videos) > 0): ?>
                     <?php foreach ($videos as $video): ?> 
                        <li class="carousel-slide">
                           <div class="video-card">
                              <video src="<?= htmlspecialchars($video['video']) ?>" controls preload></video>
                              <div class="video-info">
                                 <h3><?= htmlspecialchars($video['titulo']) ?></h3>
                                 <p><?= htmlspecialchars($video['descricao']) ?></p>
                              </div>
                           </div>
                        </li>
                     <?php endforeach; ?>
                  <?php else: ?>
                     <p style="padding:20px;">Nenhum vídeo encontrado para "<strong><?= htmlspecialchars($search) ?></strong>".</p>
                  <?php endif; ?>

               </ul>
            </div>
            <?php if (count($videos) > 3): ?>
               <button class="carousel-btn next" id="nextBtn">
                  <i class="fas fa-chevron-right"></i>
               </button>
            <?php endif; ?>
         </div>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>

</body>
<script src="../../public/js/script.js"></script>
<script src="../../public/js/videos.js"></script>

</html>
