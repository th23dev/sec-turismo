<?php
require_once __DIR__ . '/../Utils/url.php';
start_url_rewriter();

include('../Core/conexao.php');
include('../Controllers/protect.php');
require_once('../Controllers/ConteudoTuristicoController.php');
require_once('../Utils/csrf.php');

$controller = new ConteudoTuristicoController($pdo);
$categoria = (string) ($_GET['categoria'] ?? 'gastronomia');
$id = intval($_GET['id'] ?? 0);
$info = $controller->categoriaInfo($categoria);
$item = $id > 0 ? $controller->buscar($categoria, $id) : null;
$erro = '';

$csrfToken = csrf_token();
send_security_headers();

if (!$info || !$item) {
   echo 'Cadastro nao encontrado.';
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   if (!csrf_validate($_POST['csrf_token'] ?? null)) {
      $erro = 'Token CSRF invalido.';
   } elseif ($controller->atualizar($categoria, $id, $_POST, $_FILES)) {
      header('Location: ' . redirect_url('admin'));
      exit;
   } else {
      $erro = $controller->lastError() ?: 'Erro ao atualizar o cadastro. Verifique imagem, links e mapa.';
   }
}

$tituloAtual = $item['nome'] ?? $item['titulo'] ?? $info['titulo'];
$fotosSecundarias = implode("\n", array_column($item['fotos'] ?? [], 'url'));
$pratos = $item['pratos'] ?? [];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Editar <?= htmlspecialchars($tituloAtual); ?> - Turismo Curuca</title>
   <link rel="icon" type="image/webp" href="<?= asset_url('imgs/logos-bg/logo-sec-turismo.webp'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/conexao.css'); ?>">
   <link rel="stylesheet" href="<?= asset_url('css/editar.css'); ?>">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Editar <?= htmlspecialchars($info['titulo']); ?></h1>
      </div>
      <div class="btn-box">
         <a href="<?= redirect_url('admin'); ?>" class="btn-voltar"><i class="fas fa-chevron-left"></i> Voltar</a>
      </div>
   </nav>

   <main>
      <section id="section-editar">
         <div class="crud-intro">
            <div>
               <span class="crud-eyebrow">Editando</span>
               <h2><?= htmlspecialchars($tituloAtual); ?></h2>
               <p>Atualize as informacoes, revise fotos e mantenha o conteudo pronto para aparecer no catalogo publico.</p>
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
               <p class="field-hint">Envie uma nova foto ou mantenha a URL atual.</p>
               <?php if (!empty($item['imagem_principal'])): ?>
                  <div class="image-preview-box"><img class="preview-visible" src="<?= htmlspecialchars($item['imagem_principal']); ?>" alt="Preview"></div>
               <?php endif; ?>
               <div class="form-row">
                  <div class="form-group">
                     <label>Enviar nova imagem</label>
                     <input type="file" name="arquivo_imagem" accept="image/jpeg,image/png,image/webp,image/gif">
                  </div>
                  <div class="form-group">
                     <label>URL da imagem</label>
                     <input type="text" name="imagem_principal_url" placeholder="Ou cole uma URL de imagem" value="<?= htmlspecialchars($item['imagem_principal'] ?? ''); ?>">
                  </div>
               </div>
            </div>

            <div class="form-section">
               <h3><i class="fas <?= htmlspecialchars($info['icone']); ?>"></i> Informacoes</h3>
               <?php if ($categoria === 'gastronomia'): ?>
                  <div class="form-group">
                     <label>Nome do restaurante</label>
                     <input type="text" name="nome" value="<?= htmlspecialchars($item['nome'] ?? ''); ?>" required>
                  </div>
                  <div class="form-row">
                     <div class="form-group">
                        <label>Instagram</label>
                        <input type="text" name="instagram" value="<?= htmlspecialchars($item['instagram'] ?? ''); ?>" placeholder="https://instagram.com/restaurante">
                        <small class="field-hint">Use o link completo do perfil.</small>
                     </div>
                     <div class="form-group">
                        <label>Numero de telefone</label>
                        <input type="tel" name="numero" value="<?= htmlspecialchars($item['numero'] ?? ''); ?>" placeholder="(91) 99999-9999" maxlength="60" inputmode="tel">
                     </div>
                  </div>
                  <div class="form-group">
                     <label>Link do site</label>
                     <input type="text" name="site_url" value="<?= htmlspecialchars($item['site_url'] ?? ''); ?>" placeholder="https://site-ou-cardapio.com">
                  </div>
                  <div class="form-group">
                     <label>Horario de funcionamento</label>
                     <textarea name="horario_funcionamento" rows="3" placeholder="Ex: Segunda a sabado, 10h as 22h"><?= htmlspecialchars($item['horario_funcionamento'] ?? ''); ?></textarea>
                  </div>
                  <div class="form-group">
                     <label>Localizacao no Google Maps</label>
                     <textarea name="google_maps_url" rows="5" placeholder="Link ou iframe do Google Maps"><?= htmlspecialchars($item['google_maps_url'] ?? ''); ?></textarea>
                     <small class="field-hint">Iframe /maps/embed aparece incorporado no modal.</small>
                  </div>
               <?php elseif ($categoria === 'manguezais' || $categoria === 'trilha' || $categoria === 'cultura_popular'): ?>
                  <div class="form-group">
                     <label>Titulo</label>
                     <input type="text" name="titulo" value="<?= htmlspecialchars($item['titulo'] ?? ''); ?>" required>
                  </div>
               <?php endif; ?>
               <div class="form-group">
                  <label>Descricao</label>
                  <textarea name="descricao" rows="5" required><?= htmlspecialchars($item['descricao'] ?? ''); ?></textarea>
               </div>
            </div>

            <?php if ($categoria === 'manguezais'): ?>
               <div class="form-section">
                  <h3><i class="fas fa-map-location-dot"></i> Localizacao</h3>
                  <div class="form-group">
                     <label>Google Maps</label>
                     <textarea name="google_maps_url" rows="5" placeholder="Link ou iframe do Google Maps"><?= htmlspecialchars($item['google_maps_url'] ?? ''); ?></textarea>
                     <small class="field-hint">Iframe /maps/embed aparece incorporado no modal.</small>
                  </div>
               </div>
            <?php elseif ($categoria === 'trilha'): ?>
               <div class="form-section">
                  <h3><i class="fas fa-route"></i> Trajeto e links</h3>
                  <div class="form-group">
                     <label>Trajeto no Google Maps</label>
                     <textarea name="trajeto_maps_url" rows="5" placeholder="Cole o iframe completo do trajeto no Google Maps"><?= htmlspecialchars($item['trajeto_maps_url'] ?? ''); ?></textarea>
                     <small class="field-hint">Esse mapa e a visao principal da trilha.</small>
                  </div>
                  <div class="form-row">
                     <div class="form-group">
                        <label>Instagram opcional</label>
                        <input type="text" name="instagram" value="<?= htmlspecialchars($item['instagram'] ?? ''); ?>">
                     </div>
                     <div class="form-group">
                        <label>Site opcional</label>
                        <input type="text" name="site_url" value="<?= htmlspecialchars($item['site_url'] ?? ''); ?>">
                     </div>
                  </div>
               </div>
            <?php endif; ?>

            <div class="form-section">
               <h3><i class="fas fa-images"></i> Fotos secundarias</h3>
               <div class="secondary-photos-current">
                  <label>Galeria atual</label>
                  <div class="midias-grid">
                     <?php if (!empty($item['fotos'])): ?>
                        <?php foreach ($item['fotos'] as $foto): ?>
                           <?php if (!empty($foto['url'])): ?>
                              <article class="midia-card secondary-photo-card">
                                 <img src="<?= htmlspecialchars($foto['url']); ?>" alt="Foto secundaria de <?= htmlspecialchars($tituloAtual); ?>">
                                 <input type="hidden" name="fotos_secundarias[]" value="<?= htmlspecialchars($foto['url']); ?>">
                                 <button class="btn-remover-foto-secundaria" type="button" aria-label="Remover foto secundaria" data-remove-existing>
                                    <i class="fas fa-trash"></i>
                                 </button>
                              </article>
                           <?php endif; ?>
                        <?php endforeach; ?>
                     <?php else: ?>
                        <p class="midias-vazio">Nenhuma foto secundaria cadastrada ainda.</p>
                     <?php endif; ?>
                  </div>
               </div>
               <div class="form-group">
                  <label>Adicionar novas fotos</label>
                  <input type="file" name="fotos_arquivos[]" id="fotos_arquivos" accept="image/jpeg,image/png,image/webp,image/gif" multiple data-preview-target="#preview-novas-fotos-secundarias">
                  <small class="field-hint">Selecione novas fotos para ver a previa. Para remover uma foto atual, clique na lixeira da imagem e salve.</small>
               </div>
               <div class="secondary-photos-current">
                  <label>Previa das novas fotos</label>
                  <div class="midias-grid upload-preview-grid" id="preview-novas-fotos-secundarias">
                     <p class="midias-vazio">Nenhuma imagem selecionada ainda.</p>
                  </div>
               </div>
            </div>

            <?php if ($categoria === 'gastronomia'): ?>
               <div class="form-section">
                  <h3><i class="fas fa-bowl-food"></i> Pratos</h3>
                  <p class="section-note">Os pratos têm uma tela própria de criação para manter o cadastro do restaurante mais limpo.</p>
                  <div class="dish-summary-list">
                     <?php if (!empty($pratos)): ?>
                        <?php foreach ($pratos as $prato): ?>
                           <article class="dish-summary-item">
                              <?php if (!empty($prato['foto'])): ?>
                                 <img src="<?= htmlspecialchars($prato['foto']); ?>" alt="<?= htmlspecialchars($prato['nome'] ?? 'Prato'); ?>">
                              <?php else: ?>
                                 <span class="dish-summary-placeholder"><i class="fas fa-bowl-food"></i></span>
                              <?php endif; ?>
                              <div>
                                 <strong><?= htmlspecialchars($prato['nome'] ?? 'Prato sem nome'); ?></strong>
                                 <p><?= htmlspecialchars($prato['descricao'] ?? ''); ?></p>
                              </div>
                              <div class="dish-summary-actions">
                                 <a href="<?= redirect_url('editar_prato'); ?>?restaurante_id=<?= intval($id); ?>&id=<?= intval($prato['id'] ?? 0); ?>" class="btn-dish-action" aria-label="Editar prato <?= htmlspecialchars($prato['nome'] ?? ''); ?>">
                                    <i class="fas fa-pen"></i> Editar
                                 </a>
                                 <a href="<?= redirect_url('excluir_prato'); ?>?restaurante_id=<?= intval($id); ?>&id=<?= intval($prato['id'] ?? 0); ?>" class="btn-dish-action btn-dish-action--danger" aria-label="Excluir prato <?= htmlspecialchars($prato['nome'] ?? ''); ?>">
                                    <i class="fas fa-trash"></i> Excluir
                                 </a>
                              </div>
                           </article>
                        <?php endforeach; ?>
                     <?php else: ?>
                        <p class="midias-vazio">Nenhum prato cadastrado ainda.</p>
                     <?php endif; ?>
                  </div>
                  <a href="<?= redirect_url('criar_prato'); ?>?restaurante_id=<?= intval($id); ?>" class="btn-inline-add">
                     <i class="fas fa-plus"></i> Adicionar prato
                  </a>
               </div>
            <?php endif; ?>

            <div class="form-actions sticky-actions">
               <a href="<?= redirect_url('admin'); ?>" class="btn-cancelar"><i class="fas fa-times"></i> Cancelar</a>
               <a href="<?= redirect_url('excluir_conteudo'); ?>?categoria=<?= urlencode($categoria); ?>&id=<?= intval($id); ?>" class="btn-cancelar btn-excluir"><i class="fas fa-trash"></i> Excluir</a>
               <button type="submit" class="btn-salvar"><i class="fas fa-save"></i> Salvar alteracoes</button>
            </div>
         </form>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>
   <script src="<?= asset_url('js/script.js'); ?>"></script>
   <script src="<?= asset_url('js/image-preview-upload.js'); ?>"></script>
</body>

</html>
