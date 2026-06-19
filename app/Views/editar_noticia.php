<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $erro = 'Token CSRF inválido.';
    } else {
        $resultado = $controller->atualizarNoticia($id, $_POST, $_FILES);
        if ($resultado) {
            header('Location: ' . redirect_url('admin'));
            exit;
        } else {
            $erro = 'Erro ao atualizar a notícia. Tente novamente.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Notícia - <?= htmlspecialchars($noticia['titulo'] ?? '') ?></title>
   <link rel="stylesheet" href="/public/css/conexao.css">
    <link rel="stylesheet" href="/public/css/editar.css">
</head>

<body>
    <nav class="back-nav">
        <div class="text-box">
            <h1>Editar Notícia</h1>
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

            <form action="" method="post" class="editar-form" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div class="form-section">
                    <h3><i class="fas fa-heading"></i> Título</h3>
                    <div class="form-group">
                        <label for="titulo">Título da notícia</label>
                        <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($noticia['titulo'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-section image-preview-group">
                    <h3><i class="fas fa-image"></i> Imagem</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="arquivo_imagem">Upload de nova imagem</label>
                            <input type="file" name="arquivo_imagem" id="arquivo_imagem" accept="image/jpeg,image/png,image/webp,image/gif">
                            <small>Envie JPG, PNG, WebP ou GIF (máx 5MB).</small>
                        </div>
                        <div class="form-group">
                            <label for="imagem_url">Ou URL da imagem</label>
                            <input type="text" name="imagem_url" id="imagem_url" value="<?= htmlspecialchars($noticia['imagem_url'] ?? '') ?>" placeholder="https://exemplo.com/imagem.jpg">
                        </div>
                    </div>
                    <div class="image-preview-box">
                        <?php if (!empty($noticia['imagem_url'])): ?>
                            <img id="preview-img" src="<?= htmlspecialchars($noticia['imagem_url']) ?>" alt="Preview da imagem" style="display: block; width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div class="image-placeholder" id="image-placeholder">
                                <i class="fas fa-image"></i>
                                <span>Preview da imagem</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fab fa-instagram"></i> Link opcional</h3>
                    <div class="form-group input-icon">
                        <label for="instagram_url">Link do Instagram</label>
                        <i class="fab fa-instagram"></i>
                        <input type="text" name="instagram_url" id="instagram_url" value="<?= htmlspecialchars($noticia['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/exemplo">
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-calendar-day"></i> Data do evento</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="evento_data_inicio">Dia do evento</label>
                            <input type="date" name="evento_data_inicio" id="evento_data_inicio" value="<?= htmlspecialchars($noticia['evento_data_inicio'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="evento_data_fim">Termina em</label>
                            <input type="date" name="evento_data_fim" id="evento_data_fim" value="<?= htmlspecialchars($noticia['evento_data_fim'] ?? '') ?>">
                            <small>Preencha apenas se o evento acontecer em mais de um dia.</small>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-calendar-alt"></i> Período de exibição</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="data_inicio">Data de início</label>
                            <input type="datetime-local" name="data_inicio" id="data_inicio" value="<?= isset($noticia['data_inicio']) ? str_replace(' ', 'T', htmlspecialchars($noticia['data_inicio'])) : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="data_fim">Data de término</label>
                            <input type="datetime-local" name="data_fim" id="data_fim" value="<?= isset($noticia['data_fim']) ? str_replace(' ', 'T', htmlspecialchars($noticia['data_fim'])) : '' ?>">
                        </div>
                    </div>
                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="indefinido" value="1" id="indefinido" <?= (!empty($noticia['indefinido']) || empty($noticia['data_fim'])) ? 'checked' : '' ?>> Exibir indefinidamente
                        </label>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-align-left"></i> Conteúdo</h3>
                    <div class="form-group">
                        <label for="conteudo">Texto da notícia</label>
                        <textarea name="conteudo" id="conteudo" rows="8" required><?= htmlspecialchars($noticia['conteudo'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-salvar">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="/admin" class="btn-cancelar">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </section>
    </main>

    <?php include 'components/footer.php'; ?>
</body>
</html>
