<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
<?php
include('../Core/conexao.php');
include('../Controllers/protect.php');
include('../Controllers/VideoController.php');
require_once('../Utils/csrf.php');

$controller = new VideoController($pdo);
$erro = '';
$old = $_POST;

if (!isset($_SESSION)) session_start();
csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $erro = 'Token CSRF invalido.';
    } else {
        $resultado = $controller->criarVideo($_POST);
        if ($resultado) {
            header('location: ' . redirect_url('admin'));
            exit;
        }

        $erro = 'Erro ao criar o video. Use uma URL direta para MP4, WebM, OGG ou MOV.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Turismo Curuca - Criar Video</title>
   <link rel="stylesheet" href="/public/css/conexao.css">
   <link rel="stylesheet" href="/public/css/editar.css">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Criar Video</h1>
      </div>
      <div class="btn-box">
         <a href="/admin" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>
         <a href="/admin" class="btn-voltar">
            Painel <i class="fas fa-house"></i>
         </a>
      </div>
   </nav>

   <main>
      <section id="section-editar">
         <?php if ($erro): ?>
            <div class="alert alert-erro">
               <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro) ?>
            </div>
         <?php endif; ?>

         <form action="" method="post" class="editar-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="form-section">
               <h3><i class="fas fa-heading"></i> Titulo</h3>
               <div class="form-group">
                  <label for="titulo">Titulo do video</label>
                  <input type="text" name="titulo" id="titulo" placeholder="Ex: Conheca as praias de Curuca" value="<?= htmlspecialchars($old['titulo'] ?? '') ?>" required>
               </div>
            </div>

            <div class="form-section">
               <h3><i class="fas fa-link"></i> URL do video</h3>
               <div class="form-group input-icon">
                  <label for="video">Link direto do arquivo</label>
                  <i class="fas fa-video"></i>
                  <input type="text" name="video" id="video" placeholder="https://exemplo.com/video.mp4" value="<?= htmlspecialchars($old['video'] ?? '') ?>" required>
                  <small>O arquivo nao sera enviado para a pasta do site. Apenas a URL sera armazenada no banco de dados.</small>
               </div>
            </div>

            <div class="form-section">
               <h3><i class="fas fa-align-left"></i> Descricao</h3>
               <div class="form-group">
                  <label for="descricao">Descricao do video</label>
                  <textarea name="descricao" id="descricao" placeholder="Descreva o conteudo do video..." rows="6"><?= htmlspecialchars($old['descricao'] ?? '') ?></textarea>
               </div>
            </div>

            <div class="form-actions">
               <button type="submit" class="btn-salvar">
                  <i class="fas fa-plus"></i> Criar Video
               </button>
               <a href="/admin" class="btn-cancelar">
                  <i class="fas fa-times"></i> Cancelar
               </a>
            </div>
         </form>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>
   <script src="/public/js/script.js"></script>
</body>

</html>
