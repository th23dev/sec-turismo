// Menu Hambúrguer
const menuToggle = document.getElementById('menu-toggle');
const navLinks = document.getElementById('nav-links');

if (menuToggle && navLinks) {
    menuToggle.addEventListener('click', () => {
        menuToggle.classList.toggle('active');
        navLinks.classList.toggle('active');
        menuToggle.setAttribute('aria-expanded', menuToggle.getAttribute('aria-expanded') === 'false' ? 'true' : 'false');
    });

    // Fechar menu quando clicar em um link
    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            menuToggle.classList.remove('active');
            navLinks.classList.remove('active');
            menuToggle.setAttribute('aria-expanded', 'false');
        });
    });

    // Fechar menu ao redimensionar a janela
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            menuToggle.classList.remove('active');
            navLinks.classList.remove('active');
            menuToggle.setAttribute('aria-expanded', 'false');
        }
    });
}

// Dark Mode Toggle
const darkModeToggle = document.getElementById('dark-mode-toggle');
const body = document.body;

function updateDarkModeIcon() {
    if (darkModeToggle) {
        const icon = darkModeToggle.querySelector('i');
        if (!icon) {
            return;
        }

        if (body.classList.contains('dark')) {
            icon.className = 'fas fa-sun';
            darkModeToggle.classList.add('active');
        } else {
            icon.className = 'fas fa-moon';
            darkModeToggle.classList.remove('active');
        }
    }
}

// Configurar o toggle
if (darkModeToggle) {
    darkModeToggle.addEventListener('click', () => {
        body.classList.toggle('dark');
        const isDark = body.classList.contains('dark');
        localStorage.setItem('darkMode', isDark);
        updateDarkModeIcon();
    });
}

// Verificar se o modo dark está salvo no localStorage
const isDarkMode = localStorage.getItem('darkMode') === 'true';
if (isDarkMode) {
    body.classList.add('dark');
}
updateDarkModeIcon();
