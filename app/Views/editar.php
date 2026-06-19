<?php require_once __DIR__ . '/../Utils/url.php'; start_url_rewriter(); ?>
<?php
include('../Core/conexao.php');
include('../Controllers/protect.php');
include('../Controllers/LugaresController.php');
require_once('../Utils/csrf.php');

$controller = new LugaresController($pdo);

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$lugar = null;
$mensagem = isset($_GET['msg']) ? $_GET['msg'] : '';
$erro = '';

if (!isset($_SESSION)) session_start();
csrf_token();

if ($id) {
   $lugar = $controller->buscarLugar($id);
   $midias = $controller->buscarMidias($id);
}

if (!$lugar && $id) {
   echo "Lugar não encontrado!";
   exit;
}

// Processa adicionar mídia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar_midia' && $id) {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $erro = 'Token CSRF inválido.';
    } else {
        $resultado = $controller->adicionarMidias($id, $_POST, $_FILES);
        if ($resultado) {
            header('Location: ' . redirect_url('editar') . '?id=' . $id);
            exit;
        } else {
            $erro = 'Erro ao adicionar mídia. Verifique o arquivo ou URL.';
        }
    }
}

// Processa excluir mídia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'excluir_midia' && $id) {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $erro = 'Token CSRF inválido.';
    } else {
        $midia_id = intval($_POST['midia_id'] ?? 0);
        if ($midia_id > 0) {
            $controller->excluirMidia($midia_id);
            header('Location: ' . redirect_url('editar') . '?id=' . $id);
            exit;
        }
    }
}

// Processa o formulário principal se enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['acao']) && $lugar) {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $erro = 'Token CSRF inválido.';
    } else {
        $imagem_principal = '';
        if (!empty($_FILES['arquivo_imagem']['name'])) {
            $imagem_principal = 'arquivo';
        } else {
            $imagem_principal = $_POST['imagem_principal_url'] ?? '';
        }

        $resultado = $controller->atualizarLocal(
            $id,
            $imagem_principal,
            $_POST['nome'],
            $_POST['tipo'],
            $_POST['numero'],
            $_POST['instagram'],
            $_POST['linkInstagram'],
            $_POST['descricao'],
            $_POST['restaurante'],
            $_FILES
        );
        if ($resultado) {
            $mensagem = 'Local atualizado com sucesso!';
            $lugar = $controller->buscarLugar($id);
        } else {
            $erro = 'Erro ao atualizar o local.';
        }
        header('Location: ' . redirect_url('editar') . '?id=' . $id . '&msg=' . urlencode($mensagem ?: $erro));
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Turismo Curuçá - Editar <?= htmlspecialchars($lugar['nome'] ?? '') ?></title>
   <link rel="stylesheet" href="/public/css/conexao.css">
   <link rel="stylesheet" href="/public/css/editar.css">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Editar <?= htmlspecialchars($lugar['nome'] ?? '') ?></h1>
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

         <form action="" method="post" class="editar-form" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <!-- Preview da Imagem -->
            <div class="form-group image-preview-group">
               <label>Imagem Principal</label>
               
               <!-- Tabs para alternar entre upload e URL -->
               <div class="image-input-tabs">
                  <button type="button" class="tab-btn active" data-tab="upload">
                     <i class="fas fa-cloud-upload-alt"></i> Upload
                  </button>
                  <button type="button" class="tab-btn" data-tab="url">
                     <i class="fas fa-link"></i> Link
                  </button>
               </div>

               <!-- Aba Upload de Arquivo -->
               <div class="tab-content active" id="tab-upload">
                  <div class="file-upload-container">
                     <input type="file" name="arquivo_imagem" id="arquivo_imagem" accept="image/jpeg,image/png,image/webp,image/gif" style="display: none;">
                     <label for="arquivo_imagem" class="file-upload-label">
                        <i class="fas fa-image"></i>
                        <span>Clique para selecionar ou arraste a imagem</span>
                        <small>(JPG, PNG, WebP, GIF - Máx. 5MB)</small>
                     </label>
                     <input type="hidden" name="imagem_principal_arquivo" id="imagem_principal_arquivo" value="">
                  </div>
               </div>

               <!-- Aba URL da Imagem -->
               <div class="tab-content" id="tab-url">
                  <input type="text" name="imagem_principal_url" id="imagem_principal_url" placeholder="https://exemplo.com/imagem.jpg" value="<?= htmlspecialchars($lugar['imagem_principal'] ?? '') ?>">
                  <input type="hidden" name="imagem_principal" id="imagem_principal" value="">
               </div>

               <!-- Preview -->
               <div class="image-preview-box">
                  <img id="preview-img" src="<?= htmlspecialchars($lugar['imagem_principal'] ?? '') ?>" alt="Preview" <?= ($lugar['imagem_principal'] ?? '') ? '' : 'style="display: none;"' ?>>
                  <div class="image-placeholder" id="image-placeholder" style="display: <?= ($lugar['imagem_principal'] ?? '') ? 'none' : 'flex' ?>;">
                     <i class="fas fa-image"></i>
                     <span>Nenhuma imagem selecionada</span>
                  </div>
               </div>
            </div>

            <!-- Informações Básicas -->
            <div class="form-section">
               <h3><i class="fas fa-info-circle"></i> Informações Básicas</h3>
               <div class="form-row">
                  <div class="form-group">
                     <label for="nome">Nome do Local</label>
                     <input type="text" name="nome" id="nome" placeholder="Ex: Praia do Farol"
                        value="<?= htmlspecialchars($lugar['nome'] ?? '') ?>">
                  </div>
                  <div class="form-group">
                     <label for="tipo">Tipo</label>
                     <select name="tipo" id="tipo">
                        <option value="Hotel" <?= ($lugar['tipo'] ?? '') === 'Hotel' ? 'selected' : '' ?>>Hotel</option>
                        <option value="Igarapé" <?= ($lugar['tipo'] ?? '') === 'Igarape' ? 'selected' : '' ?>>Igarapé</option>
                        <option value="Praia" <?= ($lugar['tipo'] ?? '') === 'Praia' ? 'selected' : '' ?>>Praia</option>
                     </select>
                  </div>
               </div>
            </div>

            <!-- Contato -->
            <div class="form-section">
               <h3><i class="fas fa-address-book"></i> Contato</h3>
               <div class="form-row">
                  <div class="form-group">
                     <label for="numero">Número de Telefone</label>
                     <input type="text" name="numero" id="numero" placeholder="Ex: (91) 99999-9999"
                        value="<?= htmlspecialchars($lugar['numero'] ?? '') ?>">
                  </div>
                  <div class="form-group">
                     <label for="instagram">Arroba do Instagram</label>
                     <div class="input-icon">
                        <i class="fab fa-instagram"></i>
                        <input type="text" name="instagram" id="instagram" placeholder="Ex: @curuca_turismo"
                           value="<?= htmlspecialchars($lugar['instagram'] ?? '') ?>">
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <label for="linkInstagram">Link do Instagram</label>
                  <div class="input-icon">
                     <i class="fas fa-link"></i>
                     <input type="text" name="linkInstagram" id="linkInstagram" placeholder="https://instagram.com/..."
                        value="<?= htmlspecialchars($lugar['linkInstagram'] ?? '') ?>">
                  </div>
               </div>
            </div>

            <!-- Descrição -->
            <div class="form-section">
               <h3><i class="fas fa-align-left"></i> Descrição</h3>
               <div class="form-group">
                  <label for="descricao">Sobre o local</label>
                  <textarea name="descricao" id="descricao" placeholder="Descreva o local, atrações, diferenciais..." rows="5"><?= htmlspecialchars($lugar['descricao'] ?? '') ?></textarea>
               </div>
            </div>

            <!-- Restaurante -->
            <div class="form-section">
               <h3><i class="fas fa-utensils"></i> Restaurante</h3>
               <div class="form-group toggle-group">
                  <span class="toggle-label">Possui restaurante no local?</span>
                  <label class="toggle-switch">
                     <input type="checkbox" name="restaurante" value="1" id="restaurante-toggle"
                        <?= ($lugar['possui_restaurante'] ?? 0) == 1 ? 'checked' : '' ?>>
                     <span class="toggle-slider"></span>
                  </label>
                  <input type="hidden" name="restaurante" value="0" id="restaurante-hidden">
               </div>
            </div>

            <!-- Mídias -->
            <div class="form-section">
               <h3><i class="fas fa-images"></i> Mídias Adicionais</h3>
               
               <!-- Grid de mídias existentes -->
               <div class="midias-grid">
                  <?php if (!empty($midias)): ?>
                     <?php foreach ($midias as $midia): ?>
                        <div class="midia-card">
                           <img src="<?= htmlspecialchars($midia['url']) ?>" alt="Mídia" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                           <div class="midia-placeholder" style="display:none;">
                              <i class="fas fa-image"></i>
                           </div>
                           <form action="" method="post" class="midia-delete-form" onsubmit="return confirm('Tem certeza que deseja excluir esta mídia?');">
                              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                              <input type="hidden" name="acao" value="excluir_midia">
                              <input type="hidden" name="midia_id" value="<?= intval($midia['id']) ?>">
                              <button type="submit" class="btn-excluir-midia" title="Excluir mídia">
                                 <i class="fas fa-trash"></i>
                              </button>
                           </form>
                        </div>
                     <?php endforeach; ?>
                  <?php else: ?>
                     <p class="midias-vazio">Nenhuma mídia adicional cadastrada.</p>
                  <?php endif; ?>
               </div>

               <!-- Adicionar nova mídia com Tabs -->
               <div class="midia-add-box">
                  <label>Adicionar nova mídia</label>
                  
                  <!-- Tabs para alternar entre upload e URL -->
                  <div class="midia-input-tabs">
                     <button type="button" class="tab-btn active" data-tab="upload">
                        <i class="fas fa-cloud-upload-alt"></i> Upload
                     </button>
                     <button type="button" class="tab-btn" data-tab="url">
                        <i class="fas fa-link"></i> Link
                     </button>
                  </div>

                  <!-- Aba Upload de Arquivo -->
                  <div class="tab-content active" id="midia-tab-upload">
                     <div class="midia-add-row file-upload-row">
                        <input type="file" name="midias_arquivos[]" id="midias_arquivos" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
                        <small>Você pode enviar várias imagens de uma vez (JPG, PNG, WebP, GIF - Máx. 5MB cada).</small>
                     </div>
                  </div>

                  <!-- Aba URL da Mídia -->
                  <div class="tab-content" id="midia-tab-url">
                     <div class="midia-add-row">
                        <input type="text" name="url_midia" id="url_midia" placeholder="https://exemplo.com/imagem.jpg ou https://sua-api.com/imagem/123">
                        <small>Cole a URL completa da imagem ou vídeo. Aceita URLs com ou sem extensão.</small>
                     </div>
                  </div>

                  <div class="midia-add-row">
                     <button type="submit" name="acao" value="adicionar_midia" class="btn-adicionar-midia">
                        <i class="fas fa-plus"></i> Adicionar
                     </button>
                  </div>
               </div>
            </div>

            <!-- Ações -->
            <div class="form-actions">
               <button type="submit" class="btn-salvar">
                  <i class="fas fa-save"></i> Salvar Alterações
               </button>
               <a href="/admin" class="btn-cancelar">
                  <i class="fas fa-times"></i> Cancelar
               </a>
               <a href="/excluir?id=<?php echo intval($lugar['id']); ?>" class=" btn-cancelar btn-excluir">
                  <i class="fas fa-times"></i> Excluir
               </a>
            </div>
         </form>
      </section>
   </main>

   <?php include 'components/footer.php'; ?>

   <script>
      // Sistema de Tabs para Upload vs URL
      const tabButtons = document.querySelectorAll('.tab-btn');
      const tabContents = document.querySelectorAll('.tab-content');

      tabButtons.forEach(btn => {
         btn.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.dataset.tab;
            
            // Remover ativa de todos
            tabButtons.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Adicionar ativa ao clicado
            this.classList.add('active');
            document.getElementById(`tab-${tabName}`).classList.add('active');
            
            // Limpar campo do outro tab
            if (tabName === 'upload') {
               document.getElementById('imagem_principal_url').value = '';
            } else {
               document.getElementById('arquivo_imagem').value = '';
               document.getElementById('imagem_principal_arquivo').value = '';
            }
            
            updatePreview();
         });
      });

      // Upload de arquivo
      const fileInput = document.getElementById('arquivo_imagem');
      const fileLabel = document.querySelector('.file-upload-label');
      const previewImg = document.getElementById('preview-img');
      const placeholder = document.getElementById('image-placeholder');
      const imagemPrincipalArquivo = document.getElementById('imagem_principal_arquivo');

      // Drag and drop
      fileLabel.addEventListener('dragover', (e) => {
         e.preventDefault();
         fileLabel.style.backgroundColor = '#f0f0f0';
      });

      fileLabel.addEventListener('dragleave', () => {
         fileLabel.style.backgroundColor = '';
      });

      fileLabel.addEventListener('drop', (e) => {
         e.preventDefault();
         fileLabel.style.backgroundColor = '';
         fileInput.files = e.dataTransfer.files;
         handleFileSelect();
      });

      fileInput.addEventListener('change', handleFileSelect);

      function handleFileSelect() {
         if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            if (file.type.startsWith('image/')) {
               const reader = new FileReader();
               reader.onload = function(e) {
                  imagemPrincipalArquivo.value = 'arquivo_selecionado';
                  updatePreview();
               };
               reader.readAsDataURL(file);
            }
         }
      }

      // Preview da URL
      const urlInput = document.getElementById('imagem_principal_url');
      urlInput.addEventListener('input', function() {
         if (this.value.trim()) {
            document.getElementById('imagem_principal_arquivo').value = '';
            fileInput.value = '';
         }
         updatePreview();
      });

      function updatePreview() {
         const activeTab = document.querySelector('.tab-content.active').id;
         
         if (activeTab === 'tab-upload' && fileInput.files.length > 0) {
            const reader = new FileReader();
            reader.onload = function(e) {
               previewImg.src = e.target.result;
               previewImg.style.display = 'block';
               placeholder.style.display = 'none';
            };
            reader.readAsDataURL(fileInput.files[0]);
         } else if (activeTab === 'tab-url' && urlInput.value.trim()) {
            previewImg.src = urlInput.value;
            previewImg.style.display = 'block';
            placeholder.style.display = 'none';
         } else {
            previewImg.style.display = 'none';
            placeholder.style.display = 'flex';
         }
      }

      previewImg.addEventListener('error', function() {
         this.style.display = 'none';
         placeholder.style.display = 'flex';
      });

      // Toggle switch lógica
      const toggle = document.getElementById('restaurante-toggle');
      const hidden = document.getElementById('restaurante-hidden');

      toggle.addEventListener('change', function() {
         hidden.disabled = this.checked;
      });
      hidden.disabled = toggle.checked;

      // ===== SISTEMA DE ABAS PARA MÍDIAS ADICIONAIS =====
      const mediasTabButtons = document.querySelectorAll('.midia-input-tabs .tab-btn');
      const mediasTabContents = document.querySelectorAll('.midia-add-box .tab-content');

      mediasTabButtons.forEach(btn => {
         btn.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.dataset.tab;
            const container = this.closest('.midia-add-box');
            
            // Remover ativa de todos os botões e conteúdos
            container.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            container.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Adicionar ativa ao clicado
            this.classList.add('active');
            container.querySelector(`#midia-tab-${tabName}`).classList.add('active');
            
            // Limpar campos do outro tab
            if (tabName === 'upload') {
               document.getElementById('url_midia').value = '';
            } else {
               document.getElementById('midias_arquivos').value = '';
            }
         });
      });

      // Validar antes de enviar (apenas a seção de imagem principal)
      const forms = document.querySelectorAll('form.editar-form');
      forms.forEach(form => {
         form.addEventListener('submit', function(e) {
            // Validar apenas se não tiver acao (é o formulário principal)
            if (!this.querySelector('input[name="acao"]')) {
               const activeTab = document.querySelector('.image-input-tabs .active').dataset.tab;
               
               if (activeTab === 'upload' && fileInput.files.length === 0 && !document.querySelector('input[name="imagem_principal_arquivo"]').value) {
                  // Só valida se não houver imagem anterior
                  const previewImg = document.querySelector('.image-preview-box img');
                  if (!previewImg || !previewImg.src || previewImg.style.display === 'none') {
                     e.preventDefault();
                     alert('Por favor, selecione um arquivo de imagem ou mude para Link.');
                  }
               } else if (activeTab === 'url' && !urlInput.value.trim()) {
                  // Se está na aba URL, valida
                  e.preventDefault();
                  alert('Por favor, insira a URL da imagem ou mude para Upload.');
               }
            }
         });
      });
   </script>
   <script src="/public/js/script.js"></script>
</body>

</html>

