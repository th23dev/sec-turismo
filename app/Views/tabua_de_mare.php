<!DOCTYPE html>
<html lang="pt-BR">

<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Tábua de Marés — Curuçá, PA</title>
   <link rel="stylesheet" href="../../public/css/conexao.css">
</head>

<body>

   <nav class="back-nav">
      <div class="text-box">
         <h1>Praias</h1>
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

   <main class="main-content">
      <section>
         <div class="container">
            <header>
               <h1>Tábua de Marés — Curuçá, PA</h1>
               <p id="location-text">Coordenadas: 0.39°N, 0.72°E · Maré semidiurna · Fuso: Brasília (UTC-3)</p>
            </header>

            <div class="date-nav">
               <button class="btn" id="btn-prev">← Anterior</button>
               <input type="date" id="date-input" />
               <button class="btn" id="btn-next">Próximo →</button>
               <button class="btn" id="btn-hoje">Hoje</button>
            </div>

            <div id="stats-grid" class="stats-grid"></div>

            <div class="chart-container">
               <div class="chart-label">Altura da maré ao longo do dia (metros)</div>
               <div id="chart-area"></div>
            </div>

            <table class="tide-table">
               <thead>
                  <tr>
                     <th>Tipo</th>
                     <th>Horário</th>
                     <th>Altura (m)</th>
                     <th>Coeficiente</th>
                  </tr>
               </thead>
               <tbody id="tide-tbody"></tbody>
            </table>

            <p class="source" id="source-text">
               Modelo harmônico de marés semidiurnas · Open-Meteo Marine API (wave_height) · 0.39°N, 0.72°E
            </p>
         </div>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>
</body>
<script src="../../public/js/api_tabua_de_mare.js"></script>
<script src="../../public/js/script.js"></script>

</html>