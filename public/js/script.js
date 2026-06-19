const menuToggle = document.getElementById('menu-toggle');
const navLinks = document.getElementById('nav-links');
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const body = document.body;

if (menuToggle && navLinks) {
    menuToggle.addEventListener('click', () => {
        menuToggle.classList.toggle('active');
        navLinks.classList.toggle('active');
        menuToggle.setAttribute('aria-expanded', menuToggle.getAttribute('aria-expanded') === 'false' ? 'true' : 'false');
    });

    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            menuToggle.classList.remove('active');
            navLinks.classList.remove('active');
            menuToggle.setAttribute('aria-expanded', 'false');
        });
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            menuToggle.classList.remove('active');
            navLinks.classList.remove('active');
            menuToggle.setAttribute('aria-expanded', 'false');
        }
    });
}

const darkModeToggle = document.getElementById('dark-mode-toggle');

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

if (darkModeToggle) {
    darkModeToggle.addEventListener('click', () => {
        body.classList.toggle('dark');
        const isDark = body.classList.contains('dark');
        localStorage.setItem('darkMode', isDark);
        updateDarkModeIcon();
    });
}

const isDarkMode = localStorage.getItem('darkMode') === 'true';
if (isDarkMode) {
    body.classList.add('dark');
}
updateDarkModeIcon();

const revealGroups = [
    '.services-section .section-heading',
    '.service-card',
    '.home-news-header',
    '.home-news-card',
    '.home-news-empty',
    '.intro-section .section-heading',
    '.intro-section > p',
    '.experiences-section .section-heading',
    '.experience-card',
    '.feature-media',
    '.feature-content',
    '.feature-step',
    '.parent',
    '.catalogo .card',
    '.cards-grid > p',
    '.video-carousel-section .carousel-container',
    '.carousel-slide',
    '.map-intro',
    '.map-frame',
    '.info-section > .info',
    '.info-section > .info-image',
    '.passport-step',
    '.passport-rules > li',
    '.passport-preview',
    '.contact-header',
    '.contact-card',
    '.history-hero__text',
    '.history-hero__media',
    '.history-card',
    '.timeline-item',
    '.fact-card',
    '#login-section > p',
    '#login-section > form',
    '#functions-section > .container',
    '.functions',
    '.cards > .card-lugar',
    '#section-editar > .alert',
    '.editar-form > .form-group',
    '.editar-form > .form-section',
    '.editar-form > .form-actions',
    '#barraca-do-pescador'
];

const revealElements = Array.from(document.querySelectorAll(revealGroups.join(',')))
    .filter((element, index, elements) => !element.closest('.modal') && elements.indexOf(element) === index);

revealElements.forEach((element, index) => {
    element.classList.add('reveal-on-scroll');
    element.style.setProperty('--reveal-delay', `${Math.min(index % 4, 3) * 90}ms`);
});

if (reduceMotion || !('IntersectionObserver' in window)) {
    revealElements.forEach(element => element.classList.add('is-visible'));
} else {
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        rootMargin: '0px 0px -12% 0px',
        threshold: 0.12
    });

    revealElements.forEach(element => revealObserver.observe(element));
}
