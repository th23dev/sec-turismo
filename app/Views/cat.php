<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Turismo Curuçá - Portal</title>
   <link rel="stylesheet" href="/public/css/conexao.css">
   <link rel="stylesheet" href="/public/css/cat.css?v=20260618-passaporte">
</head>

<body>

   <nav class="back-nav">
      <div class="text-box">
         <h1>Centro de Atendimento Turístico</h1>
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
      <section class="info-section">
         <div class="info">
            <h2>CAT - Centro de Atendimento ao Turista</h2>
            <p>O CAT (CENTRO DE ATENDIMENTO AO TURISTA) e Secretaria Municipal de Turismo ficam localizados na praça
               Christo Alves, na Orla de Curuçá.
               Funciona todos os dias, com diversas informações.</p>
            <br>
            <p><strong>Horário de Funcionamento:</strong> Segunda a Sábado, das 08h às 18h.</p>

            <div class="social-links">
               <a class="tag insta" href="https://www.instagram.com/turismocuruca.oficial" target="_blank" rel="noopener"><i
                     class="fab fa-instagram"></i>@turismocuruca.oficial</a>
               <span class="zapp tag"><i class="fab fa-whatsapp"></i>(91) 98548-8735</span>
            </div>
         </div>
         <div class="info-image">
            <img src="/public/imgs/logos-bg/cat.webp" alt="Centro de Atendimento ao Turista">
         </div>

         <div class="info passport-info">
            <h2>Passaporte Turístico</h2>

            <p class="passport-lead">Um roteiro oficial para registrar sua passagem pelos atrativos de Curuçá e participar do circuito turístico da Terra do Folclore.</p>

            <div class="passport-flow" aria-label="Como funciona o Passaporte Turístico">
               <div class="passport-step">
                  <span>1</span>
                  <div>
                     <strong>Retire no CAT</strong>
                     <p>O passaporte está disponível presencialmente no Centro de Atendimento ao Turista.</p>
                  </div>
               </div>
               <div class="passport-step">
                  <span>2</span>
                  <div>
                     <strong>Visite e carimbe</strong>
                     <p>Complete o circuito nos atrativos e estabelecimentos parceiros credenciados.</p>
                  </div>
               </div>
               <div class="passport-step">
                  <span>3</span>
                  <div>
                     <strong>Ganhe recompensas</strong>
                     <p>Ao completar os carimbos, receba o certificado e concorra aos sorteios oficiais.</p>
                  </div>
               </div>
            </div>

            <h3>Normas de Uso do Passaporte Turístico</h3>
            <p>Para garantir que sua experiência na "Terra do Folclore" seja inesquecível, observe as seguintes diretrizes para o uso deste documento:</p>

            <ol class="passport-rules">
               <li>
                  <strong>Identificação e Pessoalidade</strong>
                  <p>O passaporte é pessoal e intransferível. Preencha seus dados de identificação na primeira página para que ele tenha validade e para que possamos devolvê-lo em caso de perda.</p>
               </li>

               <li>
                  <strong>Obtenção de Carimbos, Benefícios e Premiações</strong>
                  <p>Os carimbos podem ser adquiridos nos estabelecimentos parceiros listados neste QR Code e em pontos turísticos oficiais devidamente sinalizados.</p>
                  <p>A concessão do carimbo está sujeita à visitação do local ou, em caso de estabelecimentos comerciais, ao consumo de produtos ou serviços, conforme a política de cada parceiro.</p>
                  <p>Os estabelecimentos parceiros credenciados, dentro de suas políticas, poderão conceder brindes ou descontos nos produtos e serviços.</p>
               </li>

               <li>
                  <strong>Integridade do Documento</strong>
                  <p>Somente serão aceitos carimbos oficiais do projeto. Rasuras ou carimbos ilegíveis podem invalidar a página para fins de premiação ou benefícios futuros.</p>
                  <p><em>Cuide bem do seu passaporte! Ele é o seu diário de bordo e o registro oficial da sua jornada por Curuçá.</em></p>
               </li>

               <li>
                  <strong>Sustentabilidade e Respeito (Regra de Ouro)</strong>
                  <p>Curuçá é berço de uma rica biodiversidade. O uso deste passaporte implica o compromisso do turista com a preservação dos manguezais, o descarte correto de resíduos e o respeito absoluto às manifestações culturais e tradicionais da região.</p>
               </li>

               <li>
                  <strong>Validade</strong>
                  <p>Este passaporte tem validade até o dia <strong>31 de julho de 2027</strong> para completar sua coleção de experiências.</p>
               </li>
            </ol>
         </div>
         <div class="passport-preview" aria-label="Imagem demonstrativa do Passaporte Turístico">
            <div class="passport-preview-card">
               <img src="/public/imgs/logos-bg/passaporte_turistico.png" alt="Capa do Passaporte Turístico de Curuçá">
               <div class="passport-note">
                  <strong>Retirada presencial</strong>
                  <p>O passaporte não é disponibilizado para download. Solicite o seu diretamente no CAT.</p>
               </div>
            </div>
         </div>
      </section>

   </main>

   <?php include 'components/footer.php'; ?>

</body>
<script src="/public/js/script.js"></script>
</html>
