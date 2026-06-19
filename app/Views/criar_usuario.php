<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
<?php
include '../Core/conexao.php';
include '../Controllers/protect.php';
include '../Controllers/UsuariosController.php';
include '../Utils/csrf.php';

$controller = new UsuariosController($pdo);

$mensagem = '';
$erro = '';

csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $controller->criar($_POST);
    if (($resultado['ok'] ?? false) === true) {
        $mensagem = $resultado['message'] ?? 'Usuário criado com sucesso!';
    } else {
        $erro = $resultado['message'] ?? 'Erro ao criar usuário.';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turismo Curuçá - Criar Usuário</title>
   <link rel="stylesheet" href="/public/css/conexao.css">
   <link rel="stylesheet" href="/public/css/editar.css">
</head>

<body>
    <nav class="back-nav">
        <div class="text-box">
            <h1>Criar Usuário</h1>
        </div>
        <div class="btn-box">
            <a href="/admin" class="btn-voltar">
                <i class="fas fa-chevron-left"></i> Voltar
            </a>
            <a href="/admin" class="btn-voltar">
                Início <i class="fas fa-house"></i>
            </a>
        </div>
    </nav>

    <main>
        <section id="section-editar">
            <?php if ($mensagem): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>

            <?php if ($erro): ?>
                <div class="alert alert-erro">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="editar-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Dados do Usuário</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type="text" name="nome" id="nome" placeholder="Ex: João da Silva" required>
                        </div>

                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" name="email" id="email" placeholder="Ex: joao@email.com" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="senha">Senha</label>
                            <input type="password" name="senha" id="senha" placeholder="Mínimo 6 caracteres" required>
                        </div>

                        <div class="form-group">
                            <label for="senha_confirmacao">Confirmar Senha</label>
                            <input type="password" name="senha_confirmacao" id="senha_confirmacao" required>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-salvar">
                        <i class="fas fa-plus"></i> Criar Usuário
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

