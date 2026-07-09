document.addEventListener('DOMContentLoaded', () => {
   function bindToggle(button, container) {
      if (!button || !container) {
         return;
      }

      button.addEventListener('click', () => {
         container.classList.toggle('show');
         button.innerHTML = container.classList.contains('show')
            ? 'Ver menos <i class="fas fa-eye-slash"></i>'
            : 'Ver mais <i class="fas fa-eye"></i>';
      });
   }

   document.querySelectorAll('.ver-mais[data-target]').forEach((button) => {
      bindToggle(button, document.getElementById(button.dataset.target));
   });

   const verMaisLugares = document.getElementById('ver-mais-lugares');
   const lugares = document.getElementById('lugares');
   const verMaisNoticias = document.getElementById('ver-mais-noticias');
   const noticias = document.getElementById('noticias');
   const verMaisVideos = document.getElementById('ver-mais-videos');
   const videos = document.getElementById('videos-admin');

   if (verMaisLugares && lugares) {
      bindToggle(verMaisLugares, lugares);
   }

   if (verMaisNoticias && noticias) {
      bindToggle(verMaisNoticias, noticias);
   }

   if (verMaisVideos && videos) {
      bindToggle(verMaisVideos, videos);
   }
});
