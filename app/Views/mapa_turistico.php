<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Turismo Curu&ccedil;&aacute; - Portal</title>
   <link rel="stylesheet" href="/public/css/conexao.css">
</head>

<body>

   <nav class="back-nav">
      <div class="text-box">
         <h1>Mapa Tur&iacute;stico</h1>
      </div>
      <div class="btn-box">
         <a href="/menu" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>
         <a href="/" class="btn-voltar">
            In&iacute;cio <i class="fas fa-house"></i>
         </a>
      </div>
   </nav>

   <main class="mapa-page">
      <section id="map-section" aria-labelledby="map-title">
         <div class="map-intro">
            <span class="map-eyebrow">Explore Curu&ccedil;&aacute;</span>
            <h2 id="map-title">Mapa tur&iacute;stico da cidade</h2>
            <p>Veja os principais pontos de visita&ccedil;&atilde;o, organize seu roteiro e descubra caminhos para aproveitar melhor
               as belezas de Curu&ccedil;&aacute;.</p>

            <div class="map-actions" aria-label="A&ccedil;&otilde;es do mapa">
               <a href="/public/imgs/logos-bg/mapa_turistico.webp" class="map-action primary" target="_blank"
                  rel="noopener">
                  <i class="fas fa-up-right-from-square"></i>
                  Abrir mapa
               </a>
               <a href="/public/imgs/logos-bg/mapa_turistico.webp" class="map-action" download>
                  <i class="fas fa-download"></i>
                  Baixar
               </a>
            </div>
         </div>

         <figure class="map-frame">
            <img src="/public/imgs/logos-bg/mapa_turistico.webp" alt="Mapa Tur&iacute;stico de Curu&ccedil;&aacute;" id="map">
            <figcaption>
               <i class="fas fa-location-dot"></i>
               Principais atrativos reunidos em um mapa visual para consulta r&aacute;pida.
            </figcaption>
         </figure>
      </section>
   </main>
   <?php include 'components/footer.php'; ?>

</body>
<script src="/public/js/script.js"></script>

</html>
