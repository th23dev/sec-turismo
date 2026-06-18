<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$noticias = [];
$heroVideoUrl = 'https://mapaturisticointerativocuruca.my.canva.site/totem-site/_assets/video/b4e84df212644b4f0e6f0bcc9d9661ee.mp4';
define('DB_OPTIONAL', true);
require_once __DIR__ . '/../app/Utils/url.php';
require_once __DIR__ . '/../app/Core/conexao.php';

if (isset($pdo) && $pdo instanceof PDO) {
    require_once __DIR__ . '/../app/Controllers/NoticiasController.php';
    $noticiasController = new NoticiasController($pdo);
    $noticias = $noticiasController->buscarUltimasNoticias(3);
}

function normalizeImagemUrl(?string $path): string
{
    if (empty($path)) {
        return '';
    }
    if (preg_match('#^(https?://|data:)#i', $path)) {
        return $path;
    }

    $path = str_replace(['../../public/', '../public/', 'public/'], '', $path);

    return asset_url($path);
}

function resumoTexto(string $texto, int $limite = 150): string
{
    $texto = trim(strip_tags($texto));
    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($texto, 0, $limite, '...', 'UTF-8');
    }

    return strlen($texto) > $limite ? substr($texto, 0, $limite - 3) . '...' : $texto;
}

$experiencias = [
    [
        'titulo' => 'Praias e Ilhas',
        'texto' => 'Areia, rios, mangues e o ritmo tranquilo do litoral amazônico.',
        'imagem' => 'imgs/praias/romana/romana.webp',
        'link' => view_url('praias.php'),
    ],
    [
        'titulo' => 'Igarapés',
        'texto' => 'Banhos de água doce, natureza preservada e paradas para relaxar.',
        'imagem' => 'imgs/igarapes/aguas-verdes.webp',
        'link' => view_url('igarapes.php'),
    ],
    [
        'titulo' => 'Hospedagem',
        'texto' => 'Opções para planejar melhor a estadia e circular pela cidade.',
        'imagem' => 'imgs/hoteis/leao-de-juda/leao-de-juda.webp',
        'link' => view_url('hoteis.php'),
    ],
];

$servicos = [
    ['icone' => 'fa-map-location-dot', 'titulo' => 'Mapa turístico', 'link' => view_url('mapa_turistico.php')],
    ['icone' => 'fa-video', 'titulo' => 'Vídeos', 'link' => view_url('videos.php')],
    ['icone' => 'fa-circle-info', 'titulo' => 'CAT e passaporte', 'link' => view_url('cat.php')],
    ['icone' => 'fa-phone-volume', 'titulo' => 'Contatos úteis', 'link' => view_url('contatos_uteis.php')],
];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal turístico de Curuçá, no Pará: praias, igarapés, hospedagem, mapa turístico, notícias e serviços ao visitante.">
    <title>Turismo Curuçá - Portal Oficial</title>
    <link rel="stylesheet" href="<?= asset_url('css/style.css'); ?>?v=20260616">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset_url('css/home.css'); ?>?v=20260618-video-no-fade">
</head>

<body class="home-page">
    <nav id="main-nav" class="home-nav" aria-label="Navegação principal">
        <a class="logo-area" href="#inicio" aria-label="Turismo Curuçá">
            <img src="<?= asset_url('imgs/logos-bg/logo-visite-curuca.png'); ?>?v=20260618-logo-focus" alt="Turismo Curuçá" class="logo-img">
            <span class="logo-text">Turismo Curuçá</span>
        </a>

        <button class="menu-toggle" id="menu-toggle" aria-label="Abrir menu" aria-expanded="false" type="button">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>

        <ul class="nav-links" id="nav-links">
            <li><a href="#experiencias">Experiências</a></li>
            <li><a href="#servicos">Serviços</a></li>
            <li><a href="#noticias">Notícias</a></li>
            <li><a href="<?= view_url('menu.php'); ?>">Guia completo</a></li>
            <li>
                <button class="dark-mode-toggle" id="dark-mode-toggle" aria-label="Alternar tema" type="button">
                    <i class="fas fa-moon" aria-hidden="true"></i>
                    <span>Tema</span>
                </button>
            </li>
            <?php if (isset($_SESSION['id'])): ?>
                <li><a href="<?= view_url('admin.php'); ?>">Painel</a></li>
            <?php else: ?>
                <li><a href="<?= view_url('login.php'); ?>">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main class="home-main">
        <section class="home-hero" id="inicio" aria-label="Portal turístico de Curuçá">
            <div class="home-hero__media" aria-hidden="true">
                <?php if ($heroVideoUrl !== ''): ?>
                    <video class="home-hero__video" autoplay muted loop playsinline preload="metadata" poster="<?= asset_url('imgs/logos-bg/portal.webp'); ?>" tabindex="-1" disablepictureinpicture controlslist="nodownload nofullscreen noremoteplayback" oncontextmenu="return false;">
                        <source src="<?= htmlspecialchars($heroVideoUrl); ?>" type="video/mp4">
                    </video>
                <?php endif; ?>
            </div>
            <div class="home-hero__overlay" aria-hidden="true"></div>

            <div class="home-hero__content">
                <p class="eyebrow">Portal turístico municipal</p>
                <h1 class="sr-only">Turismo Curuçá</h1>
                <div class="hero-brand-title">
                    <img src="<?= asset_url('imgs/logos-bg/logo-visite-curuca.png'); ?>?v=20260618-logo-focus" alt="Turismo Curuçá" class="hero-brand-title__logo">
                </div>
                <div class="hero-actions">
                    <a class="btn-primary" href="<?= view_url('menu.php'); ?>">Explorar o guia</a>
                    <a class="btn-secondary" href="#experiencias">Ver experiências</a>
                </div>
            </div>

        </section>

        <section class="services-section" id="servicos">
            <div class="section-heading">
                <p class="eyebrow">Serviços</p>
                <h2>Planeje sua visita</h2>
                <p>Acesse rapidamente as informações práticas para circular, se orientar e aproveitar melhor Curuçá.</p>
            </div>

            <div class="services-grid" aria-label="Serviços ao turista">
                <?php foreach ($servicos as $servico): ?>
                    <a href="<?= htmlspecialchars($servico['link']); ?>" class="service-card">
                        <i class="fas <?= htmlspecialchars($servico['icone']); ?>" aria-hidden="true"></i>
                        <span><?= htmlspecialchars($servico['titulo']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="home-news-section" id="noticias">
            <div class="home-news-inner">
                <div class="home-news-header">
                    <p class="eyebrow">Atualizações</p>
                    <h2>Notícias e novidades</h2>
                    <p>Comunicados, eventos e informações recentes do turismo em Curuçá.</p>
                </div>
                <div class="home-news-grid <?= count($noticias) === 1 ? 'home-news-grid--single' : ''; ?>">
                    <?php if (!empty($noticias)): ?>
                        <?php foreach ($noticias as $noticia): ?>
                            <article class="home-news-card">
                                <?php if (!empty($noticia['imagem_url'])): ?>
                                    <div class="home-news-image" style="background-image: url('<?= htmlspecialchars(normalizeImagemUrl($noticia['imagem_url'])); ?>');"></div>
                                <?php else: ?>
                                    <div class="home-news-image home-news-image-placeholder">
                                        <i class="fas fa-newspaper" aria-hidden="true"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="home-news-card-content">
                                    <span class="home-news-date"><?= date('d/m/Y', strtotime($noticia['published_at'] ?? 'now')); ?></span>
                                    <h3><?= htmlspecialchars($noticia['titulo'] ?? 'Notícia'); ?></h3>
                                    <p><?= htmlspecialchars(resumoTexto($noticia['conteudo'] ?? '')); ?></p>
                                    <?php if (!empty($noticia['instagram_url'])): ?>
                                        <a href="<?= htmlspecialchars($noticia['instagram_url']); ?>" target="_blank" rel="noopener" class="home-news-instagram">
                                            <i class="fab fa-instagram" aria-hidden="true"></i> Instagram
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="home-news-empty">
                            As notícias ainda não estão disponíveis. Enquanto isso, explore o guia turístico e os serviços ao visitante.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="intro-section">
            <div class="section-heading">
                <p class="eyebrow">Turismo no litoral amazônico</p>
                <h2>Curuçá combina paisagem, tradição e acolhimento.</h2>
            </div>
            <p>Use este portal para encontrar atrativos, hospedagens, contatos úteis e informações práticas. A proposta segue a lógica de portais turísticos do Pará, com acesso rápido ao que ajuda o visitante a decidir, circular e aproveitar melhor a cidade.</p>
        </section>

        <section class="experiences-section" id="experiencias">
            <div class="section-heading">
                <p class="eyebrow">Experiências</p>
                <h2>Escolha por onde começar</h2>
            </div>

            <div class="experience-grid">
                <?php foreach ($experiencias as $experiencia): ?>
                    <a class="experience-card" href="<?= htmlspecialchars($experiencia['link']); ?>">
                        <img src="<?= htmlspecialchars(asset_url($experiencia['imagem'])); ?>" alt="">
                        <span><?= htmlspecialchars($experiencia['titulo']); ?></span>
                        <p><?= htmlspecialchars($experiencia['texto']); ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="feature-section">
            <div class="feature-content">
                <p class="eyebrow">Serviço ao visitante</p>
                <h2>Passaporte Turístico</h2>
                <p>Retire seu passaporte no Centro de Atendimento ao Turista, registre sua passagem pelos atrativos e acompanhe as regras do circuito oficial.</p>
                <div class="feature-steps" aria-label="Como usar o passaporte turistico">
                    <div class="feature-step">
                        <span>1</span>
                        <p>Retire no CAT</p>
                    </div>
                    <div class="feature-step">
                        <span>2</span>
                        <p>Visite os atrativos</p>
                    </div>
                    <div class="feature-step">
                        <span>3</span>
                        <p>Registre sua rota</p>
                    </div>
                </div>
                <div class="feature-actions">
                    <a class="text-link" href="<?= view_url('cat.php'); ?>">Saiba como funciona</a>
                </div>
            </div>
            <div class="feature-media">
                <img src="<?= asset_url('imgs/logos-bg/passaporte_turistico.png'); ?>" alt="Passaporte turístico de Curuçá">
            </div>
        </section>

    </main>

    <footer>
        <div class="footer-logos">
            <a href="https://www.sebrae.com.br/" target="_blank"><img src="<?= asset_url('imgs/logos-bg/logo-sebrae.webp'); ?>" alt="sebrae"></a>
            <a href="" target="_blank"><img src="<?= asset_url('imgs/logos-bg/logo-cidade-empreendedora.webp'); ?>" alt="cidade empreendedora"></a>
            <a href="https://www.instagram.com/turismocuruca.oficial" target="_blank"><img src="<?= asset_url('imgs/logos-bg/logo-sec-turismo.webp'); ?>" alt="secretaria de turismo Curuçá"></a>
            <a href="https://curuca.pa.gov.br/" target="_blank"><img src="<?= asset_url('imgs/logos-bg/logo-prefeitura-curuca.webp'); ?>" alt="prefeitura de Curuçá"></a>
        </div>
        <div class="social-links">
            <a href="https://www.instagram.com/turismocuruca.oficial">Siga-nos: @turismocuruca.oficial</a>
        </div>
        <hr style="border: 0.5px solid #444; margin-bottom: 20px;">
        <p>&copy; 2026 - Prefeitura Municipal de Curuçá - Todos os direitos reservados.</p>
        <p>Desenvolvedor - <a href="https://github.com/th23dev" target="_blank">Th23dev</a> - <a href="https://instagram.com/th23_dev" target="_blank">@th23_dev</a></p>
    </footer>

    <script src="<?= asset_url('js/script.js'); ?>?v=20260618-feature-layout"></script>
</body>

</html>
