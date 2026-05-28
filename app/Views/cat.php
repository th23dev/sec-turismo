<!DOCTYPE html>
<!-- SIMPLIFICAÇÃO GERAL: Evitar lógica de apresentação misturada e manter a view apenas para exibição estática. -->
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
         <h1>Centro de Atendimento Turístico</h1>
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
      <section class="info-section">
         <div class="info">
            <h2>CAT - Centro de Atendimento ao Turista</h2>
            <p>O CAT (CENTRO DE ATENDIMENTO AO TURISTA) e Secretaria Municipal de Turismo ficam localizados na praça
               Christo Alves, na Orla de Curuçá.
               Funciona todos os dias, com diversas informações.</p>
            <br>
            <p><strong>Horário de Funcionamento:</strong> Segunda a Sábado, das 08h às 18h.</p>

            <div class="social-links">
               <a class="tag insta" href="https://www.instagram.com/turismocuruca.oficial" target="_blank"><i
                     class="fab fa-instagram"></i>@turismocuruca.oficial</a>
               <span class="zapp tag"><i class="fab fa-whatsapp"></i>(91) 98548-8735</span>
            </div>
         </div>
         <div class="info-image">
            <img src="../../public/imgs/logos-bg/cat.webp" alt="Centro de Atendimento ao Turista">
         </div>
      </section>

   </main>

   <?php include 'components/footer.php'; ?>

</body>
<script src="../../public/js/script.js"></script>
</html>