<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Turismo Curuçá - Contatos Úteis</title>
   <link rel="stylesheet" href="/public/css/conexao.css">
</head>

<body>

   <nav class="back-nav">
      <div class="text-box">
         <h1>Contatos Úteis</h1>
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
      <section id="contact-section">
         <div class="contact-header">
            <span class="contact-kicker">Atendimento e emergência</span>
            <h2>Telefones para apoio ao visitante</h2>
         </div>

         <div class="contact-grid" aria-label="Lista de contatos úteis">
            <article class="contact-card is-urgent">
               <div class="contact-icon"><i class="fas fa-fire" aria-hidden="true"></i></div>
               <div class="contact-info">
                  <span class="contact-type">Emergência</span>
                  <h3>Bombeiros</h3>
                  <a href="tel:193" class="contact-phone">193</a>
               </div>
            </article>

            <article class="contact-card is-urgent">
               <div class="contact-icon"><i class="fas fa-briefcase-medical" aria-hidden="true"></i></div>
               <div class="contact-info">
                  <span class="contact-type">Emergência</span>
                  <h3>SAMU</h3>
                  <a href="tel:192" class="contact-phone">192</a>
               </div>
            </article>

            <article class="contact-card">
               <div class="contact-icon"><i class="fas fa-hospital" aria-hidden="true"></i></div>
               <div class="contact-info">
                  <span class="contact-type">Saúde</span>
                  <h3>Hospital</h3>
                  <a href="tel:+5591984560893" class="contact-phone">(91) 98456-0893</a>
               </div>
            </article>

            <article class="contact-card">
               <div class="contact-icon"><i class="fas fa-circle-info" aria-hidden="true"></i></div>
               <div class="contact-info">
                  <span class="contact-type">Turismo</span>
                  <h3>CAT</h3>
                  <p>Centro de Atendimento ao Turista</p>
                  <a href="tel:+5591985488735" class="contact-phone">(91) 98548-8735</a>
               </div>
            </article>

            <article class="contact-card">
               <div class="contact-icon"><i class="fas fa-shield-alt" aria-hidden="true"></i></div>
               <div class="contact-info">
                  <span class="contact-type">Segurança</span>
                  <h3>Polícia Militar</h3>
                  <a href="tel:+5591986312863" class="contact-phone">(91) 98631-2863</a>
               </div>
            </article>

            <article class="contact-card">
               <div class="contact-icon"><i class="fas fa-user-shield" aria-hidden="true"></i></div>
               <div class="contact-info">
                  <span class="contact-type">Segurança</span>
                  <h3>Polícia Civil</h3>
                  <a href="tel:+5591985684649" class="contact-phone">(91) 98568-4649</a>
               </div>
            </article>

            <article class="contact-card">
               <div class="contact-icon"><i class="fas fa-landmark" aria-hidden="true"></i></div>
               <div class="contact-info">
                  <span class="contact-type">Segurança</span>
                  <h3>Guarda Municipal Civil</h3>
                  <span class="contact-phone muted">Sem número informado</span>
               </div>
            </article>

            <article class="contact-card">
               <div class="contact-icon"><i class="fas fa-child" aria-hidden="true"></i></div>
               <div class="contact-info">
                  <span class="contact-type">Proteção social</span>
                  <h3>Conselho Tutelar</h3>
                  <span class="contact-phone muted">Sem número informado</span>
               </div>
            </article>

            <article class="contact-card is-urgent">
               <div class="contact-icon"><i class="fas fa-phone-volume" aria-hidden="true"></i></div>
               <div class="contact-info">
                  <span class="contact-type">Denúncia</span>
                  <h3>Violência Doméstica</h3>
                  <a href="tel:180" class="contact-phone">180</a>
               </div>
            </article>
         </div>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>

</body>
<script src="/public/js/script.js"></script>

</html>
