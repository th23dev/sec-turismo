<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
<?php
include('../Core/conexao.php');
include('../Controllers/protect.php');
include('../Controllers/LugaresController.php');
require_once('../Utils/csrf.php');

$controller = new LugaresController($pdo);

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$lugar = null;
$mensagem = '';
$erro = '';

if (!isset($_SESSION)) session_start();
csrf_token();

if ($id) {
    $lugar = $controller->buscarLugar($id);
}

if (!$lugar && $id) {
    echo "Lugar não encontrado!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_exclusao'])) {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $erro = 'Token CSRF inválido.';
    } else {
        $resultado = $controller->excluirLugar($id);
        if ($resultado) {
            header('location: ' . redirect_url('admin') . '?msg=' . urlencode('Local excluído com sucesso!'));
            exit;
        } else {
            $erro = 'Erro ao excluir o local.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Local - Turismo Curuçá</title>
   <link rel="stylesheet" href="/public/css/conexao.css">
   <link rel="stylesheet" href="/public/css/editar.css">
    <style>
        .delete-container {
            max-width: 600px;
            margin: 2rem auto;
            background: var(--secondary-bg);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .delete-header {
            background: linear-gradient(135deg, #ff4757, #ff3838);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .delete-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .delete-header .warning-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .delete-content {
            padding: 2rem;
        }

        .place-card {
            background: var(--bg);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(0, 44, 153, 0.12);
        }

        .place-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 1rem;
            border: 1px solid rgba(0, 44, 153, 0.12);
        }

        .place-details h3 {
            margin: 0 0 0.5rem 0;
            color: var(--main-text);
            font-size: 1.4rem;
            font-weight: 600;
        }

        .place-details p {
            margin: 0 0 1rem 0;
            color: var(--text-color);
            line-height: 1.5;
        }

        .place-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-color);
            font-size: 0.9rem;
        }

        .meta-item i {
            color: #6c757d;
        }

        .warning-message {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 2rem;
            color: #856404;
        }

        .warning-message .warning-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .delete-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-delete:hover {
            background: #c82333;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
            text-decoration: none;
            padding: 0.75rem 2rem;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-cancel:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        @media (max-width: 768px) {
            .delete-container {
                margin: 1rem;
                border-radius: 8px;
            }

            .delete-header,
            .delete-content {
                padding: 1.5rem;
            }

            .delete-actions {
                flex-direction: column;
            }

            .btn-delete,
            .btn-cancel {
                width: 100%;
                justify-content: center;
            }
        }

        body.dark .delete-container {
            background: var(--secondary-bg);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 16px 42px rgba(0, 0, 0, 0.35);
        }

        body.dark .place-card {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }

        body.dark .place-details h3,
        body.dark .place-details p,
        body.dark .meta-item,
        body.dark .meta-item i {
            color: var(--text-color);
        }

        body.dark .warning-message {
            background: rgba(255, 214, 0, 0.12);
            border-color: rgba(255, 214, 0, 0.32);
            color: #ffe58a;
        }

        .delete-container {
            width: min(860px, 92%);
            max-width: none;
            margin: clamp(28px, 5vw, 56px) auto;
            background: var(--secondary-bg);
            border: 1px solid rgba(0, 44, 153, 0.12);
            border-radius: 8px;
            box-shadow: 0 24px 58px rgba(0, 44, 153, 0.12);
        }

        .delete-header {
            background:
                linear-gradient(90deg, rgba(150, 20, 36, 0.96), rgba(220, 53, 69, 0.92)),
                #dc3545;
            padding: 28px;
        }

        .delete-header .warning-icon {
            display: grid;
            place-items: center;
            width: 58px;
            height: 58px;
            margin: 0 auto 14px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.14);
            font-size: 1.8rem;
        }

        .delete-header h1 {
            font-size: clamp(1.3rem, 2.5vw, 1.8rem);
            font-weight: 900;
            text-transform: uppercase;
        }

        .delete-content {
            padding: clamp(20px, 4vw, 32px);
        }

        .place-card {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 20px;
            padding: 16px;
            border: 1px solid rgba(0, 44, 153, 0.12);
            border-radius: 8px;
            background: var(--bg);
        }

        .place-image {
            width: 160px;
            min-width: 160px;
            height: 150px;
            margin: 0;
            border: 1px solid rgba(0, 44, 153, 0.12);
            border-radius: 8px;
        }

        .place-details h3 {
            color: var(--main-text);
        }

        .place-details p,
        .meta-item,
        .meta-item i {
            color: var(--text-color);
        }

        .warning-message {
            background: rgba(255, 214, 0, 0.14);
            border: 1px solid rgba(255, 214, 0, 0.36);
            border-radius: 8px;
            color: #6b4a00;
        }

        .delete-actions {
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-delete,
        .btn-cancel {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            min-width: 160px;
            border-radius: 8px;
            font-weight: 900;
        }

        @media (max-width: 680px) {
            .place-card {
                flex-direction: column;
            }

            .place-image {
                width: 100%;
                min-width: 0;
            }
        }
    </style>
</head>
<body>
    <nav class="back-nav">
        <div class="text-box">
            <h1>Excluir Local</h1>
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
                <h1>Confirmar Exclusão</h1>
            </div>

            <div class="delete-content">
                <?php if ($erro): ?>
                    <div class="alert alert-erro" style="margin-bottom: 1rem;">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro) ?>
                    </div>
                <?php endif; ?>

                <?php if ($lugar): ?>
                    <div class="warning-message">
                        <div class="warning-title">
                            <i class="fas fa-exclamation-triangle"></i> Atenção!
                        </div>
                        <p>Esta ação não pode ser desfeita. O local e todas as suas mídias serão permanentemente removidos do sistema.</p>
                    </div>

                    <div class="place-card">
                        <?php if (!empty($lugar['imagem_principal'])): ?>
                            <img src="<?= htmlspecialchars($lugar['imagem_principal']) ?>" alt="Imagem do local" class="place-image">
                        <?php endif; ?>

                        <div class="place-details">
                            <h3><?= htmlspecialchars($lugar['nome']) ?></h3>
                            <p><?= htmlspecialchars($lugar['descricao']) ?></p>

                            <div class="place-meta">
                                <div class="meta-item">
                                    <i class="fas fa-tag"></i>
                                    <span><strong>Tipo:</strong> <?= htmlspecialchars($lugar['tipo']) ?></span>
                                </div>

                                <?php if (!empty($lugar['numero'])): ?>
                                    <div class="meta-item">
                                        <i class="fas fa-phone"></i>
                                        <span><strong>Telefone:</strong> <?= htmlspecialchars($lugar['numero']) ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($lugar['possui_restaurante']): ?>
                                    <div class="meta-item">
                                        <i class="fas fa-utensils"></i>
                                        <span>Possui restaurante</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="delete-actions">
                        <form action="" method="post" style="display: inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            <input type="hidden" name="confirmar_exclusao" value="1">
                            <button type="submit" class="btn-delete">
                                <i class="fas fa-trash-alt"></i> Sim, excluir permanentemente
                            </button>
                        </form>

                        <a href="/admin" class="btn-cancel">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'components/footer.php'; ?>
</body>
</html>

