const trackContainer = document.querySelector('.carousel-track-container');
const nextBtn = document.getElementById('nextBtn');
const prevBtn = document.getElementById('prevBtn');

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

window.addEventListener('DOMContentLoaded', () => {
   videos.forEach(video => {
      video.style.transform = 'translateY(-10px)';
      video.style.opacity = 0;

      void video.offsetWidth;

      video.style.transition = 'transform 0.6s ease, opacity 0.6s ease';
      video.style.transform = 'translateY(0px)';
      video.style.opacity = 1;
   });
});
