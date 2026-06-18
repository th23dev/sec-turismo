<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
<?php
include('../Core/conexao.php');
include('../Controllers/protect.php');
include('../Controllers/VideoController.php');
require_once('../Utils/csrf.php');

$controller = new VideoController($pdo);
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$video = $id ? $controller->buscarVideo($id) : null;
$erro = '';

if (!isset($_SESSION)) session_start();
csrf_token();

if (!$id || !$video) {
    echo "Video nao encontrado!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $erro = 'Token CSRF invalido.';
    } else {
        $resultado = $controller->atualizarVideo($id, $_POST);
        if ($resultado) {
            header('Location: ' . redirect_url('admin'));
            exit;
        }

        $erro = 'Erro ao atualizar o video. Use uma URL direta para MP4, WebM, OGG ou MOV.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Editar Video - Turismo Curuca</title>
   <link rel="stylesheet" href="/public/css/conexao.css">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Editar Video</h1>
      </div>
      <div class="btn-box">
         <a href="/admin" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
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
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="form-section">
               <h3><i class="fas fa-heading"></i> Titulo</h3>
               <div class="form-group">
                  <label for="titulo">Titulo do video</label>
                  <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($video['titulo'] ?? '') ?>" required>
               </div>
            </div>

            <div class="form-section">
               <h3><i class="fas fa-link"></i> URL do video</h3>
               <div class="form-group input-icon">
                  <label for="video">Link direto do arquivo</label>
                  <i class="fas fa-video"></i>
                  <input type="text" name="video" id="video" value="<?= htmlspecialchars($video['video'] ?? '') ?>" required>
                  <small>Altere apenas a URL armazenada no banco. Nenhum arquivo sera enviado para a pasta do site.</small>
               </div>
            </div>

            <div class="form-section">
               <h3><i class="fas fa-align-left"></i> Descricao</h3>
               <div class="form-group">
                  <label for="descricao">Descricao do video</label>
                  <textarea name="descricao" id="descricao" rows="6"><?= htmlspecialchars($video['descricao'] ?? '') ?></textarea>
               </div>
            </div>

            <div class="form-actions">
               <button type="submit" class="btn-salvar">
                  <i class="fas fa-save"></i> Salvar Video
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
