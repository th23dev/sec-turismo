<?php
require_once __DIR__ . '/../Utils/url.php';
start_url_rewriter();

include('../Core/conexao.php');
include('../Controllers/protect.php');
require_once('../Controllers/ConteudoTuristicoController.php');
require_once('../Utils/csrf.php');

$controller = new ConteudoTuristicoController($pdo);
$categoria = (string) ($_GET['categoria'] ?? 'gastronomia');
$info = $controller->categoriaInfo($categoria);
$erro = '';
$descricoes = [
   'gastronomia' => 'Cadastre restaurantes com fotos, contatos, localizacao, horario de funcionamento e pratos em destaque.',
   'manguezais' => 'Crie registros de manguezais com localizacao no Google Maps e galeria de apoio.',
   'cultura_popular' => 'Organize registros culturais com descricao e fotos secundarias.',
   'trilha' => 'Monte trilhas com trajeto no Google Maps como destaque, fotos e links opcionais.',
];

$csrfToken = csrf_token();
send_security_headers();

if (!$info) {
   echo 'Categoria nao encontrada.';
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   if (!csrf_validate($_POST['csrf_token'] ?? null)) {
      $erro = 'Token CSRF invalido.';
   } elseif ($controller->criar($categoria, $_POST, $_FILES)) {
      header('Location: ' . redirect_url('admin'));
      exit;
   } else {
      $erro = $controller->lastError() ?: 'Erro ao criar o cadastro. Verifique imagem, links e mapa.';
   }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Criar <?= htmlspecialchars($info['titulo']); ?> - Turismo Curuca</title>
   <link rel="icon" type="image/webp" href="<?= asset_url('imgs/logos-bg/logo-sec-turismo.webp'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/conexao.css'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/editar.css'); ?>">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Criar <?= htmlspecialchars($info['titulo']); ?></h1>
      </div>
      <div class="btn-box">
         <a href="<?= redirect_url('admin'); ?>" class="btn-voltar"><i class="fas fa-chevron-left"></i> Voltar</a>
      </div>
   </nav>

   <main>
      <section id="section-editar">
         <div class="crud-intro">
            <div>
               <span class="crud-eyebrow">Novo cadastro</span>
               <h2><?= htmlspecialchars($info['titulo']); ?></h2>
               <p><?= htmlspecialchars($descricoes[$categoria] ?? 'Preencha os dados principais deste conteudo turistico.'); ?></p>
            </div>
            <a href="<?= redirect_url('admin'); ?>" class="crud-intro__link"><i class="fas fa-table-columns"></i> Painel</a>
         </div>

         <?php if ($erro): ?>
            <div class="alert alert-erro"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro); ?></div>
         <?php endif; ?>

         <form action="" method="post" class="editar-form content-form <?= $categoria === 'gastronomia' ? 'content-form--gastronomia' : ''; ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken); ?>">

            <div class="form-group image-preview-group crud-upload-panel">
               <label>Foto principal</label>
               <p class="field-hint">Use uma imagem horizontal e nitida. Ela sera a capa no catalogo.</p>
               <div class="file-upload-container compact-upload">
                  <input type="file" name="arquivo_imagem" id="arquivo_imagem" accept="image/jpeg,image/png,image/webp,image/gif">
               </div>
               <div class="form-group">
                  <label for="imagem_principal_url">Ou cole uma URL de imagem</label>
                  <input type="text" name="imagem_principal_url" id="imagem_principal_url" placeholder="https://exemplo.com/foto.jpg">
               </div>
            </div>

            <div class="form-section">
               <h3><i class="fas <?= htmlspecialchars($info['icone']); ?>"></i> Informacoes</h3>

               <?php if ($categoria === 'gastronomia'): ?>
                  <div class="form-group">
                     <label for="nome">Nome do restaurante</label>
                     <input type="text" name="nome" id="nome" placeholder="Ex: Restaurante Sabores de Curuca" required>
                  </div>
                  <div class="form-row">
                     <div class="form-group">
                        <label for="instagram">Instagram</label>
                        <input type="text" name="instagram" id="instagram" placeholder="https://instagram.com/restaurante">
                        <small class="field-hint">Cole o link completo do perfil.</small>
                     </div>
                     <div class="form-group">
                        <label for="numero">Numero de telefone</label>
                        <input type="tel" name="numero" id="numero" placeholder="(91) 99999-9999" maxlength="60" inputmode="tel">
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="site_url">Link do site</label>
                     <input type="text" name="site_url" id="site_url" placeholder="https://site-ou-cardapio.com">
                  </div>
                  <div class="form-group">
                     <label for="horario_funcionamento">Horario de funcionamento</label>
                     <textarea name="horario_funcionamento" id="horario_funcionamento" rows="3" placeholder="Ex: Segunda a sabado, 10h as 22h"></textarea>
                  </div>
                  <div class="form-group">
                     <label for="google_maps_url">Localizacao no Google Maps</label>
                     <textarea name="google_maps_url" id="google_maps_url" rows="5" placeholder="Cole o link ou iframe de incorporacao do Google Maps"></textarea>
                     <small class="field-hint">Se colar um iframe /maps/embed, o mapa aparece dentro do modal.</small>
                  </div>
               <?php elseif ($categoria === 'manguezais' || $categoria === 'trilha' || $categoria === 'cultura_popular'): ?>
                  <div class="form-group">
                     <label for="titulo">Titulo</label>
                     <input type="text" name="titulo" id="titulo" placeholder="Digite o titulo que aparecera no catalogo" required>
                  </div>
               <?php endif; ?>

               <div class="form-group">
                  <label for="descricao">Descricao</label>
                  <textarea name="descricao" id="descricao" rows="5" placeholder="Descreva a experiencia, caracteristicas e informacoes uteis." required></textarea>
               </div>
            </div>

            <?php if ($categoria === 'manguezais'): ?>
               <div class="form-section">
                  <h3><i class="fas fa-map-location-dot"></i> Localizacao</h3>
                  <div class="form-group">
                     <label for="google_maps_url">Google Maps</label>
                     <textarea name="google_maps_url" id="google_maps_url" rows="5" placeholder="Cole o link ou iframe de incorporacao do Google Maps"></textarea>
                     <small class="field-hint">Se colar um iframe /maps/embed, o mapa aparece dentro do modal.</small>
                  </div>
               </div>
            <?php elseif ($categoria === 'trilha'): ?>
               <div class="form-section">
                  <h3><i class="fas fa-route"></i> Trajeto e links</h3>
                  <div class="form-group">
                     <label for="trajeto_maps_url">Trajeto no Google Maps</label>
                     <textarea name="trajeto_maps_url" id="trajeto_maps_url" rows="5" placeholder="Cole o iframe completo do trajeto no Google Maps"></textarea>
                     <small class="field-hint">Esse trajeto vira a visao principal da trilha.</small>
                  </div>
                  <div class="form-row">
                     <div class="form-group">
                        <label for="instagram">Instagram opcional</label>
                        <input type="text" name="instagram" id="instagram" placeholder="https://instagram.com/...">
                     </div>
                     <div class="form-group">
                        <label for="site_url">Site opcional</label>
                        <input type="text" name="site_url" id="site_url" placeholder="https://...">
                     </div>
                  </div>
               </div>
            <?php endif; ?>

            <div class="form-section">
               <h3><i class="fas fa-images"></i> Fotos secundarias</h3>
               <div class="form-group">
                  <label for="fotos_arquivos">Enviar fotos</label>
                  <input type="file" name="fotos_arquivos[]" id="fotos_arquivos" accept="image/jpeg,image/png,image/webp,image/gif" multiple data-preview-target="#preview-fotos-secundarias">
                  <small class="field-hint">Selecione uma ou mais fotos. A previa aparece abaixo antes de salvar.</small>
               </div>
               <div class="secondary-photos-current">
                  <label>Previa das fotos selecionadas</label>
                  <div class="midias-grid upload-preview-grid" id="preview-fotos-secundarias">
                     <p class="midias-vazio">Nenhuma imagem selecionada ainda.</p>
                  </div>
               </div>
            </div>

            <?php if ($categoria === 'gastronomia'): ?>
               <div class="form-section">
                  <h3><i class="fas fa-bowl-food"></i> Pratos</h3>
                  <p class="section-note">Primeiro salve o restaurante. Depois, na tela de edição, use o botão "Adicionar prato" para cadastrar cada prato em uma tela própria.</p>
               </div>
            <?php endif; ?>

            <div class="form-actions sticky-actions">
               <a href="<?= redirect_url('admin'); ?>" class="btn-cancelar"><i class="fas fa-times"></i> Cancelar</a>
               <button type="submit" class="btn-salvar"><i class="fas fa-plus"></i> Criar cadastro</button>
            </div>
         </form>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>
   <script src="<?= asset_url('js/script.js'); ?>"></script>
   <script src="<?= asset_url('js/image-preview-upload.js'); ?>"></script>
</body>

</html>
