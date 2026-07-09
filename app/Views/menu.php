<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Turismo Curu&ccedil;&aacute; - Portal</title>
   <link rel="icon" type="image/webp" href="/public/imgs/logos-bg/logo-sec-turismo.webp">
   <link rel="stylesheet" href="<?= asset_url('css/conexao.css'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/menu.css'); ?>">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Menu Tur&iacute;stico</h1>
      </div>

      <div class="btn-box">
         <a href="<?= app_url('/'); ?>" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar para o in&iacute;cio
         </a>
      </div>
   </nav>

   <main class="main-container">
      <section class="parent">
         <a href="<?= view_url('videos.php'); ?>" id="videos">V&iacute;deos</a>

         <a href="<?= view_url('hoteis.php'); ?>" id="hoteis">Hot&eacute;is</a>

         <a href="<?= view_url('mapa_turistico.php'); ?>" id="mapa-turistico">Mapa Tur&iacute;stico</a>

         <a href="<?= view_url('barraca_do_pescador.php'); ?>" id="barraca-pescador">Barraca do Pescador</a>

         <a href="https://www.instagram.com/turismocuruca.oficial?igsh=MTdlbnEyMm13ZWVzeg%3D%3D" id="SEMTUR"
            class="insta" target="_blank" rel="noopener"><img src="<?= asset_url('imgs/logos-bg/logo-sec-turismo.webp'); ?>" alt="">SEMTUR</a>

         <a href="https://www.instagram.com/prefeituracuruca" id="PREFEITURA" class="insta" target="_blank" rel="noopener"><img
               src="<?= asset_url('imgs/logos-bg/logo-prefeitura-curuca.webp'); ?>" alt="">Prefeitura</a>

         <a href="https://www.instagram.com/mturismo/" id="MTUR" class="insta" target="_blank" rel="noopener"><img
               class="mtur-icon" src="<?= asset_url('imgs/logos-bg/logo-mtur-turismo.svg'); ?>" alt="Turismo MTur">MTUR</a>

         <a href="https://www.instagram.com/sebraepa/" id="SEBRAE" class="insta" target="_blank" rel="noopener"><img
               src="<?= asset_url('imgs/logos-bg/logo-sebrae.webp'); ?>" alt="SEBRAE"></a>

         <a href="https://www.instagram.com/embraturbrasil" id="EMBRATUR" class="insta" target="_blank" rel="noopener"><img
               class="embratur-icon" src="<?= asset_url('imgs/logos-bg/logo-embratur-60.svg'); ?>" alt="60 anos Embratur">EMBRATUR</a>

         <a href="https://www.instagram.com/icmbio.salgadoparaense?utm_source=ig_web_button_share_sheet&amp;igsh=ZDNlZDc0MzIxNw==" id="SALGADO" class="insta salgado-link" target="_blank" rel="noopener">
            <img class="icmbio-icon" src="<?= asset_url('imgs/logos-bg/logo-icmbio-mma.svg'); ?>" alt="ICMBio MMA">
            <span class="salgado-label">NGI Salgado<br>Paraense</span>
         </a>

         <a href="<?= view_url('igarapes.php'); ?>" id="igarapes">Igarap&eacute;s</a>

         <a href="https://tabuademares.com/br/para/curuca#_tabela_mares" id="tabua-de-mare">T&aacute;bua de Mar&eacute;</a>

         <a href="<?= view_url('cat.php'); ?>" id="cat">Centro de Atendimento ao Turista</a>

         <a href="<?= view_url('praias.php'); ?>" id="praias">Praias</a>

         <a href="<?= view_url('historia.php'); ?>" id="contatos-uteis">Hist&oacute;ria de Curu&ccedil;&aacute;</a>

         <a href="<?= view_url('manguezais.php'); ?>" id="manguezais">
            <span>Manguezais</span>
         </a>

         <a href="<?= view_url('gastronomia.php'); ?>" id="gastronomia">
            <span>Gastronomia</span>
         </a>

         <a href="<?= view_url('cultura_popular.php'); ?>" id="cultura-popular">
            <span>Cultura Popular</span>
         </a>

         <a href="<?= view_url('trilhas.php'); ?>" id="trilhas">
            <span>Trilhas</span>
         </a>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>

</body>
<script src="<?= asset_url('js/script.js'); ?>"></script>

</html>
