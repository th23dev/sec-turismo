document.addEventListener('DOMContentLoaded', () => {
   const verMaisLugares = document.getElementById('ver-mais-lugares');
   const lugares = document.getElementById('lugares');
   const verMaisNoticias = document.getElementById('ver-mais-noticias');
   const noticias = document.getElementById('noticias');

   if (verMaisLugares && lugares) {
      verMaisLugares.addEventListener('click', () => {
         lugares.classList.toggle('show');

         if (lugares.classList.contains('show')) {
            verMaisLugares.innerHTML = 'Ver menos <i class="fas fa-eye-slash"></i>';
         } else {
            verMaisLugares.innerHTML = 'Ver mais <i class="fas fa-eye"></i>';
         }
      });
   }

   if (verMaisNoticias && noticias) {
      verMaisNoticias.addEventListener('click', () => {
         noticias.classList.toggle('show');

         if (noticias.classList.contains('show')) {
            verMaisNoticias.innerHTML = 'Ver menos <i class="fas fa-eye-slash"></i>';
         } else {
            verMaisNoticias.innerHTML = 'Ver mais <i class="fas fa-eye"></i>';
         }
      });
   }
});