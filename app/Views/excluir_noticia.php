<?php
include('../Core/conexao.php');
include('../Controllers/protect.php');
include('../Controllers/NoticiasController.php');
require_once('../Utils/csrf.php');

$controller = new NoticiasController($pdo);
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$noticia = null;
$erro = '';

if (!isset($_SESSION)) session_start();
csrf_token();

if ($id) {
    $noticia = $controller->buscarNoticia($id);
}

if (!$noticia && $id) {
    echo "Notícia não encontrada!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_exclusao'])) {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $erro = 'Token CSRF inválido.';
    } else {
        $resultado = $controller->excluirNoticia($id);
        if ($resultado) {
            header('location: admin.php');
            exit;
        } else {
            $erro = 'Erro ao excluir a notícia.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Notícia - Turismo Curuçá</title>
    <link rel="stylesheet" href="../../public/css/conexao.css">
    <link rel="stylesheet" href="../../public/css/editar.css">
</head>
<body>
    <nav class="back-nav">
        <div class="text-box">
            <h1>Excluir Notícia</h1>
        </div>
        <div class="btn-box">
            <a href="admin.php" class="btn-voltar">
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
                <h1>Confirmar Exclusão</h1>
            </div>

            <div class="delete-content">
                <?php if ($erro): ?>
                    <div class="alert alert-erro" style="margin-bottom: 1rem;">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro) ?>
                    </div>
                <?php endif; ?>

                <?php if ($noticia): ?>
                    <div class="warning-message">
                        <div class="warning-title">
                            <i class="fas fa-exclamation-triangle"></i> Atenção!
                        </div>
                        <p>Esta ação não pode ser desfeita. A notícia será permanentemente removida.</p>
                    </div>

                    <div class="place-card">
                        <?php if (!empty($noticia['imagem_url'])): ?>
                            <img src="<?= htmlspecialchars($noticia['imagem_url']) ?>" alt="Imagem da notícia" class="place-image">
                        <?php endif; ?>
                        <div class="place-details">
                            <h3><?= htmlspecialchars($noticia['titulo']) ?></h3>
                            <p><?= nl2br(htmlspecialchars($noticia['conteudo'])) ?></p>
                        </div>
                    </div>

                    <form method="post" class="delete-actions">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" name="confirmar_exclusao" class="btn-delete">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                        <a href="admin.php" class="btn-cancel">
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
