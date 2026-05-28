function openModal(rio) {
   const modalId = rio;
   const modal = document.getElementById("modal-" + modalId);
   modal.style.display = "flex";
   updateCarousel(modalId, 0); // Inicializar visibilidade das setas

   // Adicionar event listener para fechar ao clicar fora
   modal.addEventListener('click', function(event) {
      if (event.target === modal) {
         closeModal(modalId);
      }
   });
}

function closeModal(rio) {
   const modal = document.getElementById("modal-" + rio);
   // Pausar todos os vídeos no modal antes de fechar
   const videos = modal.querySelectorAll('video');
   videos.forEach(video => video.pause());
   modal.style.display = "none";
}

function prevImage(modalId) {
   const modal = document.getElementById("modal-" + modalId);
   const carouselImages = modal.querySelector('.carousel-images');
   const items = carouselImages.querySelectorAll('.carousel-image, .carousel-video');
   const indicators = modal.querySelectorAll('.indicator');
   let currentIndex = Array.from(indicators).findIndex(ind => ind.classList.contains('active'));
   currentIndex = (currentIndex - 1 + items.length) % items.length;
   updateCarousel(modalId, currentIndex);
}

function nextImage(modalId) {
   const modal = document.getElementById("modal-" + modalId);
   const carouselImages = modal.querySelector('.carousel-images');
   const items = carouselImages.querySelectorAll('.carousel-image, .carousel-video');
   const indicators = modal.querySelectorAll('.indicator');
   let currentIndex = Array.from(indicators).findIndex(ind => ind.classList.contains('active'));
   currentIndex = (currentIndex + 1) % items.length;
   updateCarousel(modalId, currentIndex);
}

function goToImage(modalId, index) {
   updateCarousel(modalId, index);
}

function updateCarousel(modalId, index) {
   const modal = document.getElementById("modal-" + modalId);
   const carouselImages = modal.querySelector('.carousel-images');
   const prevBtn = modal.querySelector('.carousel-btn.prev');
   const nextBtn = modal.querySelector('.carousel-btn.next');
   const indicatorsContainer = modal.querySelector('.carousel-indicators');
   const items = carouselImages.querySelectorAll('.carousel-image, .carousel-video');

   // Limpar indicadores existentes
   indicatorsContainer.innerHTML = '';

   // Criar indicadores dinamicamente
   items.forEach((_, i) => {
      const indicator = document.createElement('span');
      indicator.classList.add('indicator');
      if (i === index) indicator.classList.add('active');
      indicator.onclick = () => goToImage(modalId, i);
      indicatorsContainer.appendChild(indicator);
   });

   carouselImages.style.transform = `translateX(-${index * 100}%)`;

   // Pausar todos os vídeos
   items.forEach(item => {
      if (item.classList.contains('carousel-video')) {
         const video = item.querySelector('video');
         if (video) video.pause();
      }
   });

   // Tocar o vídeo atual se for vídeo
   const currentItem = items[index];
   if (currentItem && currentItem.classList.contains('carousel-video')) {
      const video = currentItem.querySelector('video');
      if (video) video.play();
   }

   // Controlar visibilidade das setas e indicadores
   if (items.length <= 1) {
      prevBtn.style.display = 'none';
      nextBtn.style.display = 'none';
      indicatorsContainer.style.display = 'none';
   } else {
      prevBtn.style.display = index === 0 ? 'none' : 'flex';
      nextBtn.style.display = index === items.length - 1 ? 'none' : 'flex';
      indicatorsContainer.style.display = 'flex';
   }
}