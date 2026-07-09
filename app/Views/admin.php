<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
<?php
include('../Core/conexao.php');
include('../Controllers/protect.php');
include('../Controllers/LugaresController.php');
require_once('../Controllers/NoticiasController.php');
require_once('../Controllers/VideoController.php');
require_once('../Controllers/ConteudoTuristicoController.php');

$controller = new LugaresController($pdo);
$noticiasController = new NoticiasController($pdo);
$videosController = new VideoController($pdo);
$conteudoController = new ConteudoTuristicoController($pdo);

$tiposPermitidos = ['hotel', 'igarape', 'praia'];
$tipo = isset($_GET['tipo']) && in_array($_GET['tipo'], $tiposPermitidos, true) ? $_GET['tipo'] : null;

if ($tipo) {
   $lugares = $controller->buscarLugares($tipo);
} else {
   $lugares = $controller->buscarTodosOsLugares();
}

$noticias = $noticiasController->buscarNoticias();
$videos = $videosController->buscarVideos('');
$conteudosTuristicos = [];
foreach ($conteudoController->categorias() as $categoriaSlug => $categoriaInfo) {
   $conteudosTuristicos[$categoriaSlug] = [
      'info' => $categoriaInfo,
      'itens' => $conteudoController->buscarTodos($categoriaSlug),
   ];
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Turismo Curuca - Portal</title>
   <link rel="icon" type="image/webp" href="/public/imgs/logos-bg/logo-sec-turismo.webp">
   <link rel="stylesheet" href="/public/css/conexao.css">
   <link rel="stylesheet" href="/public/css/admin.css">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Painel Administrativo</h1>
      </div>

      <div class="btn-box">
         <a href="/" class="btn-voltar">
            <i class="fas fa-chevron-left"></i>
         </a>
         <?php if (isset($_SESSION['nome'])): ?>
            <div id="user-name">Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</div>
            <a href="/logout" class="btn-logout">
               <i class="fas fa-sign-out-alt"></i>
            </a>
         <?php endif; ?>
      </div>
   </nav>

   <main>
      <section id="functions-section">
         <div class="container" id="lugares">
            <div class="functions">
               <h2>Lugares</h2>

               <div class="filtros">
                  <a href="/admin?tipo=hotel">Hoteis</a>
                  <a href="/admin?tipo=igarape">Igarapes</a>
                  <a href="/admin?tipo=praia">Praias</a>
                  <a href="/admin">Todos</a>
               </div>

               <button class="ver-mais" id="ver-mais-lugares">Ver mais <i class="fas fa-eye"></i></button>
            </div>

            <div class="cards">
               <a href="/criar" class="card-lugar" id="add-lugar"><i class="fas fa-plus"></i></a>
               <?php foreach ($lugares as $lugar): ?>
                  <?php if (!empty($lugar['imagem_principal'])): ?>
                     <div class="card-lugar" style="background: url('<?= htmlspecialchars($lugar['imagem_principal']); ?>') no-repeat center center / cover;">
                  <?php else: ?>
                     <div class="card-lugar card-lugar-empty">
                        <i class="fas fa-image"></i>
                  <?php endif; ?>
                     <?php echo htmlspecialchars($lugar['nome']); ?>
                     <a href="/editar?id=<?php echo intval($lugar['id']); ?>"><i class="fas fa-pencil"></i></a>
                  </div>
               <?php endforeach ?>
            </div>
         </div>

         <?php foreach ($conteudosTuristicos as $categoriaSlug => $grupo): ?>
            <?php $containerId = 'conteudo-' . str_replace('_', '-', $categoriaSlug); ?>
            <div class="container conteudo-admin-section" id="<?= htmlspecialchars($containerId); ?>">
               <div class="functions">
                  <h2><?= htmlspecialchars($grupo['info']['titulo']); ?></h2>
                  <div class="filtros">
                     <span><?= count($grupo['itens']); ?> cadastrados</span>
                  </div>
                  <button class="ver-mais" type="button" data-target="<?= htmlspecialchars($containerId); ?>">
                     Ver mais <i class="fas fa-eye"></i>
                  </button>
               </div>

               <div class="cards">
                  <a href="/criar_conteudo?categoria=<?= urlencode($categoriaSlug); ?>" class="card-lugar card-add" title="Adicionar <?= htmlspecialchars($grupo['info']['titulo']); ?>">
                     <i class="fas <?= htmlspecialchars($grupo['info']['icone']); ?>"></i>
                  </a>
                  <?php foreach ($grupo['itens'] as $item): ?>
                     <?php
                     $fotoPrincipal = $item['imagem_principal'] ?? '';
                     if ($fotoPrincipal === '' && !empty($item['fotos'][0]['url'])) {
                        $fotoPrincipal = $item['fotos'][0]['url'];
                     }
                     ?>
                     <?php if ($fotoPrincipal !== ''): ?>
                        <div class="card-lugar" style="background: url('<?= htmlspecialchars($fotoPrincipal); ?>') no-repeat center center / cover;">
                     <?php else: ?>
                        <div class="card-lugar card-lugar-empty">
                           <i class="fas <?= htmlspecialchars($grupo['info']['icone']); ?>"></i>
                     <?php endif; ?>
                        <?php
                        $tituloItem = $item[$grupo['info']['title_column'] ?? ''] ?? '';
                        if ($tituloItem === '') {
                           $tituloItem = mb_strimwidth(strip_tags($item['descricao'] ?? 'Cadastro'), 0, 42, '...');
                        }
                        ?>
                        <?= htmlspecialchars($grupo['info']['titulo']); ?>: <?= htmlspecialchars($tituloItem); ?>
                        <a href="/editar_conteudo?categoria=<?= urlencode($categoriaSlug); ?>&id=<?= intval($item['id']); ?>"><i class="fas fa-pencil"></i></a>
                     </div>
                  <?php endforeach; ?>
               </div>
            </div>
         <?php endforeach; ?>

         <div class="container" id="noticias">
            <div class="functions">
               <h2>Noticias</h2>
               <div class="filtros"></div>
               <button class="ver-mais" id="ver-mais-noticias">Ver mais <i class="fas fa-eye"></i></button>
            </div>

            <div class="cards">
               <a href="/criar_noticia" class="card-lugar" id="add-noticia"><i class="fas fa-plus"></i></a>
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
                           <div class="card-title"><?= htmlspecialchars($noticia['titulo']); ?></div>
                           <div class="card-actions">
                              <a href="/editar_noticia?id=<?php echo intval($noticia['id']); ?>" class="icon-btn edit" title="Editar"><i class="fas fa-pencil"></i></a>
                              <a href="/excluir_noticia?id=<?php echo intval($noticia['id']); ?>" class="icon-btn delete" title="Excluir"><i class="fas fa-trash"></i></a>
                           </div>
                        </div>
                     </div>
                  <?php endforeach; ?>
               <?php else: ?>
                  <div class="card-lugar card-lugar-empty">
                     <i class="fas fa-bell"></i>
                     Nenhuma noticia cadastrada ainda.
                  </div>
               <?php endif; ?>
            </div>
         </div>

         <div class="container" id="videos-admin">
            <div class="functions">
               <h2>Videos</h2>
               <div class="filtros video-summary">
                  <span><?php echo count($videos); ?> cadastrados</span>
               </div>
               <button class="ver-mais" id="ver-mais-videos">Ver mais <i class="fas fa-eye"></i></button>
            </div>

            <div class="cards">
               <a href="/criar_video" class="card-lugar card-add" id="add-video" title="Adicionar video"><i class="fas fa-plus"></i></a>
               <?php if (!empty($videos)): ?>
                  <?php foreach ($videos as $video): ?>
                     <div class="card-lugar card-video">
                        <div class="card-bg placeholder">
                           <i class="fas fa-play"></i>
                        </div>
                        <div class="card-content">
                           <div class="card-title"><?= htmlspecialchars($video['titulo']); ?></div>
                           <div class="card-actions">
                              <a href="/editar_video?id=<?php echo intval($video['id']); ?>" class="icon-btn edit" title="Editar"><i class="fas fa-pencil"></i></a>
                              <?php if (is_safe_http_url($video['video'] ?? '')): ?>
                                 <a href="<?= htmlspecialchars($video['video']); ?>" class="icon-btn open" title="Abrir video" target="_blank" rel="noopener"><i class="fas fa-up-right-from-square"></i></a>
                              <?php endif; ?>
                              <a href="/excluir_video?id=<?php echo intval($video['id']); ?>" class="icon-btn delete" title="Excluir"><i class="fas fa-trash"></i></a>
                           </div>
                        </div>
                     </div>
                  <?php endforeach; ?>
               <?php else: ?>
                  <div class="card-lugar card-lugar-empty">
                     <i class="fas fa-video"></i>
                     Nenhum video cadastrado ainda.
                  </div>
               <?php endif; ?>
            </div>
         </div>

         <div class="container" id="usuarios">
            <div class="functions">
               <h2>Usuarios</h2>
               <div class="filtros"></div>
               <div></div>
            </div>

            <div class="cards">
               <a href="/criar_usuario" class="card-lugar" id="add-usuario" title="Adicionar usuario">
                  <i class="fas fa-user-plus"></i>
               </a>
               <div class="card-lugar card-lugar-empty">
                  <i class="fas fa-user"></i>
                  Cadastre novos usuarios
               </div>
            </div>
         </div>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>

</body>
<script src="/public/js/script.js"></script>
<script src="/public/js/admin.js"></script>

</html>
