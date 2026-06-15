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

         <div class="info">
            <h2>Passaporte Turístico</h2>

            <h3>Como funciona o Passaporte Turístico?</h3>
            <ul>
               <li><strong>Adquira seu Passaporte:</strong> Disponível no Centro de Atendimento ao Turista (CAT).</li>
               <li><strong>Ganhe Recompensas:</strong> Ao completar o circuito (todos os carimbos), você receberá um Certificado de Amigo do Turismo e, ao final do ano de 2026, concorrerá a sorteios de brindes e prêmios.</li>
            </ul>

            <h3>Normas de Uso do Passaporte Turístico</h3>
            <p>Para garantir que sua experiência na "Terra do Folclore" seja inesquecível, observe as seguintes diretrizes para o uso deste documento:</p>

            <ol>
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
                  <p>Este passaporte tem validade até o dia <strong>31 de dezembro de 2026</strong> para completar sua coleção de experiências.</p>
               </li>
            </ol>
         </div>
         <div class="info-image">
            <div style="text-align: center; margin-bottom: 10px;">
               <button id="prev-btn" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Anterior</button>
               <span id="page-num-display" style="margin: 0 15px; font-size: 16px; font-family: sans-serif;">Página: 1 / 1</span>
               <button id="next-btn" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Próximo</button>
            </div>

            <div id="pdf-view-window" style="width: 100%; max-width: 700px; margin: 0 auto; overflow: hidden; background: #eee; border: 1px solid #ccc;">

               <div id="pdf-container" style="display: flex; transition: transform 0.3s ease-in-out;"></div>

            </div>

            <template id="page-template">
               <canvas class="pdf-page" style="width: 100%; flex-shrink: 0; background: white; box-sizing: border-box;"></canvas>
            </template>
         </div>
      </section>

   </main>

   <?php include 'components/footer.php'; ?>

</body>
<script src="../../public/js/script.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

<script>
   const url = '../../public/documents/passaporte_turistico.pdf';

   pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

   const container = document.getElementById('pdf-container');
   const template = document.getElementById('page-template');

   // Elementos de controle
   const prevBtn = document.getElementById('prev-btn');
   const nextBtn = document.getElementById('next-btn');
   const pageDisplay = document.getElementById('page-num-display');

   let currentPage = 1;
   let totalPages = 0;

   // 1. Carrega o documento completo
   pdfjsLib.getDocument(url).promise.then(pdf => {
      totalPages = pdf.numPages;
      updateControls();

      // Cria todas as páginas em background de uma vez
      for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
         renderPage(pdf, pageNum);
      }
   }).catch(error => {
      console.error("Erro ao carregar o PDF: ", error);
   });

   function renderPage(pdf, pageNum) {
      pdf.getPage(pageNum).then(page => {
         const clone = template.content.cloneNode(true);
         const canvas = clone.querySelector('.pdf-page');
         const context = canvas.getContext('2d');

         // Escala padrão
         const viewport = page.getViewport({
            scale: 1.5
         });
         canvas.height = viewport.height;
         canvas.width = viewport.width;

         const renderContext = {
            canvasContext: context,
            viewport: viewport
         };

         page.render(renderContext).promise.then(() => {
            container.appendChild(canvas);
         });
      });
   }

   // --- LÓGICA DE NAVEGAÇÃO (Mover o container usando CSS Translate) ---

   function moveToPage(pageIndex) {
      // Como as páginas estão lado a lado, movemos o container 100% para a esquerda por página
      const displacement = -(pageIndex - 1) * 100;
      container.style.transform = `translateX(${displacement}%)`;

      currentPage = pageIndex;
      updateControls();
   }

   function updateControls() {
      pageDisplay.textContent = `Página: ${currentPage} / ${totalPages}`;

      // Bloqueia os botões nas extremidades
      prevBtn.disabled = (currentPage === 1);
      nextBtn.disabled = (currentPage === totalPages);
   }

   // Eventos dos botões
   prevBtn.addEventListener('click', () => {
      if (currentPage > 1) {
         moveToPage(currentPage - 1);
      }
   });

   nextBtn.addEventListener('click', () => {
      if (currentPage < totalPages) {
         moveToPage(currentPage + 1);
      }
   });
</script>

</html>