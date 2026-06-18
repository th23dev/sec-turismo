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
         <h1>Barraca do Pescador</h1>
      </div>
      <div class="btn-box">
         <a href="/menu" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>
         <a href="/" class="btn-voltar">
            Início <i class="fas fa-house"></i>
         </a>
      </div>
   </nav>

   <main>
      <iframe id="barraca-do-pescador" src="https://sabor-curuca-pescador.base44.app/Home" frameborder="0"></iframe>
   </main>

   <?php include 'components/footer.php'; ?>

</body>
<script src="/public/js/script.js"></script>
</html>