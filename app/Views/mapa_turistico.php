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
         <h1>Mapa Turístico</h1>
      </div>
      <div class="btn-box">
         <a href="menu.php" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>
         <a href="../../public/index.php" class="btn-voltar">
            Início <i class="fas fa-house"></i>
         </a>
      </div>
   </nav>

   <main>
      <section id="map-section">
         <img src="../../public/imgs/logos-bg/mapa_turistico.webp" alt="Mapa Turístico" id="map">
         <p>Mapa turístico da cidade de Curuçá, aqui você pode ficar por dentro de todas as principais atrações dessa
            bela cidade.</p>
      </section>
   </main>
   <?php include 'components/footer.php'; ?>

</body>
<script src="../../public/js/script.js"></script>
</html>