<?php
// SIMPLIFICAÇÃO GERAL: Usar um controller para preparar os dados e evitar queries diretas nas views.
include('../Core/conexao.php');
include('../Controllers/protect.php');
include('../Controllers/LugaresController.php');
require_once('../Controllers/NoticiasController.php');

$controller = new LugaresController($pdo);
$noticiasController = new NoticiasController($pdo);

// Verifica se existe filtro via GET
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;

if ($tipo) {
   $lugares = $controller->buscarLugares($tipo);
} else {
   $lugares = $controller->buscarTodosOsLugares();
}

$noticias = $noticiasController->buscarNoticias();

?>

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
         <h1>Painel Administrativo</h1>
      </div>

      <div class="btn-box">
         <a href="../../public/index.php" class="btn-voltar">
            <i class="fas fa-chevron-left"></i>
         </a>
         <?php if (isset($_SESSION['nome'])): ?>
            <div id="user-name">Bem-vindo, <?php echo $_SESSION['nome']; ?>!</div>
            <a href="../Controllers/logout.php" class="btn-logout">
               <i class="fas fa-sign-out-alt"></i></a>
         <?php endif; ?>
      </div>
   </nav>

   <main>
      <section id="functions-section">
         <div class="container" id="lugares">
            <div class="functions">
               <h2>Lugares</h2>

               <div class="filtros">
                  <a href="admin.php?tipo=hotel">Hotéis</a>
                  <a href="admin.php?tipo=igarape">Igarapés</a>
                  <a href="admin.php?tipo=praia">Praias</a>
                  <a href="admin.php">Todos</a>
               </div>

               <button class="ver-mais" id="ver-mais-lugares">Ver mais <i class="fas fa-eye"></i></button>
            </div>

            <div class="cards">
               <a href="criar.php" class="card-lugar" id="add-lugar"><i class="fas fa-plus"></i></a>
               <?php foreach ($lugares as $lugar): ?>
                  <?php if (!empty($lugar['imagem_principal'])): ?>
                     <div class="card-lugar" style="background: url('<?= htmlspecialchars($lugar['imagem_principal']); ?>') no-repeat center center / cover;">
                  <?php else: ?>
                     <div class="card-lugar card-lugar-empty">
                        <i class="fas fa-image"></i>
                  <?php endif; ?>
                     <?php echo htmlspecialchars($lugar['nome']); ?> 
                     <a href="editar.php?id=<?php echo $lugar['id']; ?>"><i class="fas fa-pencil"></i></a>
                  </div>
               <?php endforeach ?>
            </div>
         </div>
      </section>

      <section id="news-section">
         <div class="container" id="noticias">
            <div class="functions">
               <h2>Notícias</h2>
               <div class="filtros"></div>
               <button class="ver-mais" id="ver-mais-noticias">Ver mais <i class="fas fa-eye"></i></button>
            </div>

            <div class="cards">
               <a href="criar_noticia.php" class="card-lugar" id="add-noticia"><i class="fas fa-plus"></i></a>
               <?php if (!empty($noticias)): ?>
                  <?php foreach ($noticias as $noticia): ?>
                     <div class="card-lugar card-noticia">
                        <?php if (!empty($noticia['imagem_url'])): ?>
                           <div class="card-bg" style="background: url('<?= htmlspecialchars($noticia['imagem_url']); ?>') no-repeat center center / cover;"></div>
                        <?php else: ?>
                           <div class="card-bg placeholder">
                              <i class="fas fa-newspaper"></i>
                           </div>
                        <?php endif; ?>
                        <div class="card-content">
                           <span class="news-label"><?= date('d/m/Y', strtotime($noticia['published_at'])); ?></span>
                           <div class="card-title"><?= htmlspecialchars($noticia['titulo']); ?></div>
                           <div class="card-actions">
                              <a href="editar_noticia.php?id=<?php echo $noticia['id']; ?>" class="icon-btn edit" title="Editar"><i class="fas fa-pencil"></i></a>
                              <a href="excluir_noticia.php?id=<?php echo $noticia['id']; ?>" class="icon-btn delete" title="Excluir"><i class="fas fa-trash"></i></a>
                           </div>
                        </div>
                     </div>
                  <?php endforeach; ?>
               <?php else: ?>
                  <div class="card-lugar card-lugar-empty">
                     <i class="fas fa-bell"></i>
                     Nenhuma notícia cadastrada ainda.
                  </div>
               <?php endif; ?>
            </div>
         </div>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>

</body>
<script src="../../public/js/script.js"></script>
<script src="../js/menu.js"></script>
<script src="../../public/js/admin.js"></script>

</html>