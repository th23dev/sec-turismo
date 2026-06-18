<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Turismo Curuçá - Portal</title>
   <link rel="stylesheet" href="/public/css/conexao.css">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Menu Turístico</h1>
      </div>

      <div class="btn-box">
         <a href="/" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar para o início
         </a>
      </div>
   </nav>

   <main class="main-container">

      <section class="parent">
         <a href="/videos" id="videos">Vídeos</a>

         <a href="/hoteis" id="hoteis">Hotéis</a>

         <a href="/mapa_turistico" id="mapa-turistico">Mapa Turisístico Animado</a>

         <a href="/barraca_do_pescador" id="barraca-pescador">Barraca do Pescador</a>

         <a href="https://www.instagram.com/turismocuruca.oficial?igsh=MTdlbnEyMm13ZWVzeg%3D%3D" id="SEMTUR"
            class="insta" target="_blank"><img src="/public/imgs/logos-bg/logo-sec-turismo.webp" alt=""></i>SEMTUR</a>

         <a href="https://www.instagram.com/prefeituracuruca" id="PREFEITURA" class="insta" target="_blank"><img
               src="/public/imgs/logos-bg/logo-prefeitura-curuca.webp" alt="">Prefeitura</a>

         <a href="https://www.instagram.com/mturismo/" id="MTUR" class="insta" target="_blank"><i
               class="fab fa-instagram"></i>MTUR</a>

         <a href="https://www.instagram.com/sebraepa/" id="SEBRAE" class="insta" target="_blank"><img
               src="/public/imgs/logos-bg/logo-sebrae.webp" alt="">SEBRAE</a>

         <a href="https://www.instagram.com/embraturbrasil" id="EMBRATUR" class="insta" target="_blank"><i
               class="fab fa-instagram"></i>EMBRATUR</a>

         <a href="/igarapes" id="igarapes">Igarapés</a>

         <a href="https://tabuademares.com/br/para/curuca#_tabela_mares" id="tabua-de-mare">Tábua de Maré</a>

         <a href="/cat" id="cat">Centro de Atendimento ao Turista</a>

         <a href="/praias" id="praias">Praias</a>

         <a href="/contatos_uteis" id="contatos-uteis">Contatos Úteis</a>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>

</body>
<script src="/public/js/script.js"></script>

</html>
