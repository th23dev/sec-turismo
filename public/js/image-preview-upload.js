function renderSelectedImagePreviews(input) {
   const targetSelector = input.dataset.previewTarget;
   const target = targetSelector ? document.querySelector(targetSelector) : null;
   if (!target) {
      return;
   }

   target.innerHTML = '';

   const files = Array.from(input.files || []);
   if (!files.length) {
      target.innerHTML = '<p class="midias-vazio">Nenhuma imagem selecionada ainda.</p>';
      return;
   }

   files.forEach((file, index) => {
      const card = document.createElement('article');
      card.className = 'midia-card secondary-photo-card preview-photo-card';

      const image = document.createElement('img');
      image.alt = file.name;
      image.src = URL.createObjectURL(file);
      image.onload = () => URL.revokeObjectURL(image.src);

      const button = document.createElement('button');
      button.className = 'btn-remover-foto-secundaria';
      button.type = 'button';
      button.setAttribute('aria-label', 'Remover foto selecionada');
      button.innerHTML = '<i class="fas fa-trash"></i>';
      button.addEventListener('click', () => {
         removeFileFromInput(input, index);
         renderSelectedImagePreviews(input);
      });

      card.appendChild(image);
      card.appendChild(button);
      target.appendChild(card);
   });
}

function removeFileFromInput(input, indexToRemove) {
   const dataTransfer = new DataTransfer();
   Array.from(input.files || []).forEach((file, index) => {
      if (index !== indexToRemove) {
         dataTransfer.items.add(file);
      }
   });
   input.files = dataTransfer.files;
}

document.querySelectorAll('[data-preview-target]').forEach((input) => {
   renderSelectedImagePreviews(input);
   input.addEventListener('change', () => renderSelectedImagePreviews(input));
});

document.querySelectorAll('.btn-remover-foto-secundaria[data-remove-existing]').forEach((button) => {
   button.addEventListener('click', () => {
      button.closest('.secondary-photo-card')?.remove();
   });
});
