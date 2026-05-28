<?php
// SIMPLIFICAÇÃO GERAL: Usar um pattern MVC mais claro e mover validações e tokens CSRF para um middleware/construtor comum.
include('../Core/conexao.php');
include('../Controllers/protect.php');
include('../Controllers/LugaresController.php');
require_once('../Utils/ImageUpload.php');

$controller = new LugaresController($pdo);
$mensagem = '';
$erro = '';

if (!isset($_SESSION)) session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $erro = 'Token CSRF inválido.';
    } else {
        $resultado = $controller->criarLocal($_POST, $_FILES);
        if ($resultado) {
            $mensagem = 'Local criado com sucesso!';
            header('location: admin.php');
            exit;
        } else {
            $erro = 'Erro ao criar o local. Tente novamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Turismo Curuçá - Criar Novo Local</title>
   <link rel="stylesheet" href="../../public/css/conexao.css">
</head>

<body>
   <nav class="back-nav">
      <div class="text-box">
         <h1>Criar Novo Local</h1>
      </div>
      <div class="btn-box">
         <a href="admin.php" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar
         </a>
         <a href="admin.php" class="btn-voltar">
            Início <i class="fas fa-house"></i>
         </a>
      </div>
   </nav>

   <main>
      <section id="section-editar">
         <?php if ($mensagem): ?>
            <div class="alert alert-success">
               <i class="fas fa-check-circle"></i> <?= $mensagem ?>
            </div>
         <?php endif; ?>
         <?php if ($erro): ?>
            <div class="alert alert-erro">
               <i class="fas fa-exclamation-circle"></i> <?= $erro ?>
            </div>
         <?php endif; ?>

         <!-- ERRO: Ausência de proteção CSRF. Risco: Ataques de cross-site request forgery. Solução: gerar token CSRF na sessão e validar em todo POST. -->
         <form action="" method="post" class="editar-form" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
                  <input type="text" name="imagem_principal_url" id="imagem_principal_url" placeholder="https://exemplo.com/imagem.jpg">
                  <input type="hidden" name="imagem_principal" id="imagem_principal" value="">
               </div>

               <!-- Preview -->
               <div class="image-preview-box">
                  <img id="preview-img" src="" alt="Preview" style="display: none;">
                  <div class="image-placeholder" id="image-placeholder" style="display: flex;">
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
                     <input type="text" name="nome" id="nome" placeholder="Ex: Praia do Farol" required>
                  </div>
                  <div class="form-group">
                     <label for="tipo">Tipo</label>
                     <select name="tipo" id="tipo">
                        <option value="Hotel">Hotel</option>
                        <option value="Igarapé">Igarapé</option>
                        <option value="Praia">Praia</option>
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
                     <input type="text" name="numero" id="numero" placeholder="Ex: (91) 99999-9999" required>
                  </div>
                  <div class="form-group">
                     <label for="instagram">Arroba do Instagram</label>
                     <div class="input-icon">
                        <i class="fab fa-instagram"></i>
                        <input type="text" name="instagram" id="instagram" placeholder="Ex: @curuca_turismo" required>
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <label for="linkInstagram">Link do Instagram</label>
                  <div class="input-icon">
                     <i class="fas fa-link"></i>
                     <input type="text" name="linkInstagram" id="linkInstagram" placeholder="https://instagram.com/..." required>
                  </div>
               </div>
            </div>

            <!-- Descrição -->
            <div class="form-section">
               <h3><i class="fas fa-align-left"></i> Descrição</h3>
               <div class="form-group">
                  <label for="descricao">Sobre o local</label>
                  <textarea name="descricao" id="descricao" placeholder="Descreva o local, atrações, diferenciais..." rows="5" required></textarea>
               </div>
            </div>

            <!-- Restaurante -->
            <div class="form-section">
               <h3><i class="fas fa-utensils"></i> Restaurante</h3>
               <div class="form-group toggle-group">
                  <span class="toggle-label">Possui restaurante no local?</span>
                  <label class="toggle-switch">
                     <input type="checkbox" name="restaurante" value="1" id="restaurante-toggle">
                     <span class="toggle-slider"></span>
                  </label>
                  <input type="hidden" name="restaurante" value="0" id="restaurante-hidden">
               </div>
            </div>

            <!-- Mídias -->
            <div class="form-section">
               <h3><i class="fas fa-images"></i> Mídias Adicionais</h3>
               <div class="form-group">
                  <label for="midias">URLs de imagens/vídeos adicionais (uma por linha)</label>
                  <textarea name="midias" id="midias" placeholder="https://exemplo.com/imagem1.jpg&#10;https://exemplo.com/imagem2.jpg&#10;https://exemplo.com/video.mp4" rows="5"></textarea>
               </div>
            </div>

            <!-- Ações -->
            <div class="form-actions">
               <button type="submit" class="btn-salvar">
                  <i class="fas fa-plus"></i> Criar Local
               </button>
               <a href="admin.php" class="btn-cancelar">
                  <i class="fas fa-times"></i> Cancelar
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

      // Validar antes de enviar
      document.querySelector('.editar-form').addEventListener('submit', function(e) {
         const activeTab = document.querySelector('.image-input-tabs .active').dataset.tab;
         
         if (activeTab === 'upload' && fileInput.files.length === 0) {
            e.preventDefault();
            alert('Por favor, selecione um arquivo de imagem.');
         } else if (activeTab === 'url' && !urlInput.value.trim()) {
            e.preventDefault();
            alert('Por favor, insira a URL da imagem.');
         }
      });
   </script>
   <script src="../../public/js/script.js"></script>
   <script src="../js/menu.js"></script>
</body>

</html>

