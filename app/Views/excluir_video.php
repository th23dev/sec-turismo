<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
<?php
include('../Core/conexao.php');
include('../Controllers/protect.php');
include('../Controllers/VideoController.php');
require_once('../Utils/csrf.php');

$controller = new VideoController($pdo);
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$video = null;
$erro = '';

if (!isset($_SESSION)) session_start();
csrf_token();

if ($id) {
    $video = $controller->buscarVideo($id);
}

if (!$id || !$video) {
    echo "Video nao encontrado!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_exclusao'])) {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $erro = 'Token CSRF invalido.';
    } else {
        $resultado = $controller->excluirVideo($id);
        if ($resultado) {
            header('location: ' . redirect_url('admin'));
            exit;
        }

        $erro = 'Erro ao excluir o video.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Excluir Video - Turismo Curuca</title>
   <link rel="stylesheet" href="/public/css/conexao.css">
   <link rel="stylesheet" href="/public/css/editar.css">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Excluir Video</h1>
      </div>
      <div class="btn-box">
         <a href="/admin" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>
      </div>
   </nav>

   <main>
      <div class="delete-container">
         <div class="delete-header">
            <div class="warning-icon">
               <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1>Confirmar Exclusao</h1>
         </div>

         <div class="delete-content">
            <?php if ($erro): ?>
               <div class="alert alert-erro" style="margin-bottom: 1rem;">
                  <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro) ?>
               </div>
            <?php endif; ?>

            <?php if ($video): ?>
               <div class="warning-message">
                  <div class="warning-title">
                     <i class="fas fa-exclamation-triangle"></i> Atencao!
                  </div>
                  <p>Esta acao remove apenas o registro do banco. Nenhum arquivo sera apagado da pasta do site.</p>
               </div>

               <div class="place-card">
                  <div class="place-details">
                     <h3><?= htmlspecialchars($video['titulo']) ?></h3>
                     <p><?= nl2br(htmlspecialchars($video['descricao'] ?? '')) ?></p>
                     <p><strong>URL:</strong> <?= htmlspecialchars($video['video']) ?></p>
                  </div>
               </div>

               <form method="post" class="delete-actions">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                  <button type="submit" name="confirmar_exclusao" class="btn-delete">
                     <i class="fas fa-trash"></i> Excluir
                  </button>
                  <a href="/admin" class="btn-cancel">
                     <i class="fas fa-times"></i> Cancelar
                  </a>
               </form>
            <?php endif; ?>
         </div>
      </div>
   </main>

   <?php include 'components/footer.php'; ?>
</body>

</html>
