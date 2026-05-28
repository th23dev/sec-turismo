const trackContainer = document.querySelector('.carousel-track-container');
const nextBtn = document.getElementById('nextBtn');
const prevBtn = document.getElementById('prevBtn');

// Função para rolar o carrossel baseado na largura de um slide
const getSlideWidth = () => document.querySelector('.carousel-slide').offsetWidth;

nextBtn.addEventListener('click', () => {
   trackContainer.scrollBy({
      left: getSlideWidth(),
      behavior: 'smooth'
   });
});

prevBtn.addEventListener('click', () => {
   trackContainer.scrollBy({
      left: -getSlideWidth(),
      behavior: 'smooth'
   });
});

const videos = document.querySelectorAll('.carousel-slide');

// Executa a animação quando a página é carregada
window.addEventListener('DOMContentLoaded', () => {
   videos.forEach(video => {
      video.style.transform = 'translateY(-10px)';
      video.style.opacity = 0;

      // Força o reflow para garantir que a transição seja aplicada
      void video.offsetWidth;

      video.style.transition = 'transform 0.6s ease, opacity 0.6s ease';
      video.style.transform = 'translateY(0px)';
      video.style.opacity = 1;
   });
});
