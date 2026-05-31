<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon.svg">
    <link rel="stylesheet" href="/api/theme.css">
    <link rel="stylesheet" href="/assets/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="canonical" href="https://sebastiaanpeters.nl<?= escapeHtml($currentPath ?? '/') ?>">
    <title><?= escapeHtml($title ?? 'Home') ?> | Sebastiaan Peters</title>
    <meta name="description" content="<?= escapeHtml($description ?? 'Professioneel binnen- en buitenschilderwerk in de regio Utrecht.') ?>">

    <meta property="og:title" content="<?= escapeHtml($title ?? 'Home') ?> | Sebastiaan Peters">
    <meta property="og:description" content="<?= escapeHtml($description ?? 'Professioneel binnen- en buitenschilderwerk in de regio Utrecht.') ?>">
    <meta property="og:url" content="https://sebastiaanpeters.nl<?= escapeHtml($currentPath ?? '/') ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="https://sebastiaanpeters.nl<?= escapeHtml($ogImage ?? '/sebastiaan-profiel.jpg') ?>">
    <meta property="og:site_name" content="Sebastiaan Peters">
    <meta property="og:locale" content="nl_NL">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= escapeHtml($title ?? 'Home') ?> | Sebastiaan Peters">
    <meta name="twitter:description" content="<?= escapeHtml($description ?? 'Professioneel binnen- en buitenschilderwerk in de regio Utrecht.') ?>">
    <meta name="twitter:image" content="https://sebastiaanpeters.nl<?= escapeHtml($ogImage ?? '/sebastiaan-profiel.jpg') ?>">

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "@id": "https://sebastiaanpeters.nl",
        "name": "Sebastiaan Peters",
        "description": "Professioneel binnen- en buitenschilderwerk",
        "url": "https://sebastiaanpeters.nl",
        "telephone": "<?= escapeHtml($businessInfo['phone'] ?? '') ?>",
        "email": "<?= escapeHtml($businessInfo['email'] ?? '') ?>",
        "image": "https://sebastiaanpeters.nl<?= escapeHtml($ogImage ?? '/sebastiaan-profiel.jpg') ?>",
        "address": { "@type": "PostalAddress", "addressLocality": "Utrecht", "addressCountry": "NL" },
        "areaServed": "Regio Utrecht, Nederland",
        "priceRange": "€€"
    }
    </script>
</head>
<body class="bg-white text-neutral-900 font-sans antialiased">
    <div class="noise-overlay" aria-hidden="true"></div>

    <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-lg border-b border-neutral-100/50">
        <nav class="mx-auto max-w-[1400px] px-6 h-18 flex items-center justify-between">
            <a href="/" class="text-lg font-bold tracking-tight">
                Sebastiaan<span class="text-accent-1 font-light">Peters</span>
            </a>
            <div class="hidden md:flex items-center gap-10">
                <a href="/projecten" class="text-sm font-medium transition-colors relative py-1 <?= str_starts_with($currentPath ?? '', '/projecten') ? 'text-accent-1' : 'text-neutral-600 hover:text-neutral-900' ?>">
                    Projecten
                    <?php if (str_starts_with($currentPath ?? '', '/projecten')): ?><span class="absolute -bottom-px left-0 right-0 h-px bg-accent-1"></span><?php endif; ?>
                </a>
                <a href="/over-mij" class="text-sm font-medium transition-colors relative py-1 <?= ($currentPath ?? '') === '/over-mij' ? 'text-accent-1' : 'text-neutral-600 hover:text-neutral-900' ?>">
                    Over mij
                    <?php if (($currentPath ?? '') === '/over-mij'): ?><span class="absolute -bottom-px left-0 right-0 h-px bg-accent-1"></span><?php endif; ?>
                </a>
                <a href="/partners" class="text-sm font-medium transition-colors relative py-1 <?= ($currentPath ?? '') === '/partners' ? 'text-accent-1' : 'text-neutral-600 hover:text-neutral-900' ?>">
                    Partners
                    <?php if (($currentPath ?? '') === '/partners'): ?><span class="absolute -bottom-px left-0 right-0 h-px bg-accent-1"></span><?php endif; ?>
                </a>
                <a href="/tarieven" class="text-sm font-medium transition-colors relative py-1 <?= ($currentPath ?? '') === '/tarieven' ? 'text-accent-1' : 'text-neutral-600 hover:text-neutral-900' ?>">
                    Tarieven
                    <?php if (($currentPath ?? '') === '/tarieven'): ?><span class="absolute -bottom-px left-0 right-0 h-px bg-accent-1"></span><?php endif; ?>
                </a>
                <a href="/contact" class="px-5 py-2.5 bg-accent-1 text-white text-sm font-medium rounded-full hover:bg-accent-2 active:scale-[0.97] transition-all duration-300 ease-spring">Offerte aanvragen</a>
            </div>
            <button id="menu-toggle" class="md:hidden flex flex-col gap-1 p-2 relative z-[70]" aria-label="Menu openen">
                <span class="block w-6 h-[1.5px] bg-neutral-900 transition-all duration-300 ease-spring"></span>
                <span class="block w-6 h-[1.5px] bg-neutral-900 transition-all duration-300 ease-spring"></span>
                <span class="block w-6 h-[1.5px] bg-neutral-900 transition-all duration-300 ease-spring"></span>
            </button>
        </nav>
    </header>

    <div id="mobile-menu" class="md:hidden fixed inset-0 z-40 bg-white/95 backdrop-blur-xl translate-x-full transition-transform duration-500 ease-spring">
        <div class="flex flex-col justify-center h-full px-6 gap-8 pt-18 pb-12 w-full overflow-y-auto">
            <a href="/projecten" class="relative text-center text-2xl sm:text-3xl font-medium tracking-tight transition-all duration-500 ease-spring translate-y-4 opacity-0 <?= str_starts_with($currentPath ?? '', '/projecten') ? 'text-accent-1' : 'text-neutral-800 hover:text-accent-1' ?>" style="transition-delay: 100ms" data-nav-item data-nav-link>
                Projecten
                <?php if (str_starts_with($currentPath ?? '', '/projecten')): ?><span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-8 h-px bg-accent-1"></span><?php endif; ?>
            </a>
            <a href="/over-mij" class="relative text-center text-2xl sm:text-3xl font-medium tracking-tight transition-all duration-500 ease-spring translate-y-4 opacity-0 <?= ($currentPath ?? '') === '/over-mij' ? 'text-accent-1' : 'text-neutral-800 hover:text-accent-1' ?>" style="transition-delay: 160ms" data-nav-item data-nav-link>Over mij</a>
            <a href="/partners" class="relative text-center text-2xl sm:text-3xl font-medium tracking-tight transition-all duration-500 ease-spring translate-y-4 opacity-0 <?= ($currentPath ?? '') === '/partners' ? 'text-accent-1' : 'text-neutral-800 hover:text-accent-1' ?>" style="transition-delay: 220ms" data-nav-item data-nav-link>Partners</a>
            <a href="/tarieven" class="relative text-center text-2xl sm:text-3xl font-medium tracking-tight transition-all duration-500 ease-spring translate-y-4 opacity-0 <?= ($currentPath ?? '') === '/tarieven' ? 'text-accent-1' : 'text-neutral-800 hover:text-accent-1' ?>" style="transition-delay: 280ms" data-nav-item data-nav-link>Tarieven</a>
            <div class="text-center transition-all duration-500 ease-spring translate-y-4 opacity-0" style="transition-delay: 360ms" data-nav-item>
                <a href="/contact" class="inline-flex items-center px-6 py-3 bg-accent-1 text-white font-medium rounded-full hover:bg-accent-2 active:scale-[0.97] transition-all duration-300 ease-spring" data-nav-link>Offerte aanvragen</a>
            </div>
        </div>
    </div>

    <main class="pt-18"><?= $content ?></main>

    <footer class="border-t border-neutral-100 mt-32">
        <div class="mx-auto max-w-[1400px] px-6 py-20">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                <div>
                    <p class="text-lg font-bold tracking-tight mb-5">Sebastiaan<span class="text-accent-1 font-light">Peters</span></p>
                    <p class="text-sm text-neutral-500 leading-relaxed max-w-sm"><?= escapeHtml($businessInfo['intro'] ?? '') ?></p>
                    <div class="flex gap-4 mt-8">
                        <a href="https://wa.me/<?= preg_replace('/[^\d]/', '', $businessInfo['phone'] ?? '') ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-neutral-100 text-neutral-600 hover:bg-accent-1 hover:text-white transition-all duration-300 ease-spring" aria-label="WhatsApp">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                        <a href="mailto:<?= escapeHtml($businessInfo['email'] ?? '') ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-neutral-100 text-neutral-600 hover:bg-accent-1 hover:text-white transition-all duration-300 ease-spring" aria-label="E-mail">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4L12 13 2 4"/></svg>
                        </a>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-10">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400 mb-5">Navigatie</p>
                        <ul class="space-y-3">
                            <li><a href="/projecten" class="text-sm text-neutral-600 hover:text-accent-1 transition-colors">Projecten</a></li>
                            <li><a href="/over-mij" class="text-sm text-neutral-600 hover:text-accent-1 transition-colors">Over mij</a></li>
                            <li><a href="/partners" class="text-sm text-neutral-600 hover:text-accent-1 transition-colors">Partners</a></li>
                            <li><a href="/tarieven" class="text-sm text-neutral-600 hover:text-accent-1 transition-colors">Tarieven</a></li>
                            <li><a href="/contact" class="text-sm text-neutral-600 hover:text-accent-1 transition-colors">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400 mb-5">Contact</p>
                        <ul class="space-y-3">
                            <li class="text-sm text-neutral-600"><?= escapeHtml($businessInfo['phone'] ?? '') ?></li>
                            <li class="text-sm text-neutral-600"><?= escapeHtml($businessInfo['email'] ?? '') ?></li>
                            <li class="text-sm text-neutral-600"><?= escapeHtml($businessInfo['location'] ?? '') ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-neutral-100 py-6">
            <div class="mx-auto max-w-[1400px] px-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-xs text-neutral-400">&copy; <?= date('Y') ?> Sebastiaan Peters. Alle rechten voorbehouden.</p>
                <div class="flex gap-6">
                    <a href="/privacy" class="text-xs text-neutral-400 hover:text-neutral-600 transition-colors">Privacy beleid</a>
                    <a href="/algemene-voorwaarden" class="text-xs text-neutral-400 hover:text-neutral-600 transition-colors">Algemene voorwaarden</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        (function() {
            var toggle = document.getElementById('menu-toggle');
            var menu = document.getElementById('mobile-menu');
            var links = document.querySelectorAll('[data-nav-link]');
            var isOpen = false;
            var lastFocus = null;

            function closeMenu() {
                isOpen = false; menu.classList.remove('open'); menu.classList.add('translate-x-full'); menu.classList.remove('translate-x-0');
                toggle.setAttribute('aria-label', 'Menu openen');
                var spans = toggle.querySelectorAll('span'); spans[0].style.transform = ''; spans[1].style.opacity = ''; spans[2].style.transform = '';
                document.body.style.overflow = ''; if (lastFocus) { lastFocus.focus(); lastFocus = null; }
            }

            if (toggle && menu) {
                toggle.addEventListener('click', function() {
                    isOpen = !isOpen; menu.classList.toggle('open', isOpen); menu.classList.toggle('translate-x-full', !isOpen); menu.classList.toggle('translate-x-0', isOpen);
                    toggle.setAttribute('aria-label', isOpen ? 'Menu sluiten' : 'Menu openen');
                    var spans = toggle.querySelectorAll('span');
                    if (isOpen) { spans[0].style.transform = 'rotate(45deg) translate(3px, 5px)'; spans[1].style.opacity = '0'; spans[2].style.transform = 'rotate(-45deg) translate(3px, -5px)'; document.body.style.overflow = 'hidden'; lastFocus = document.activeElement; if (links.length > 0) links[0].focus(); }
                    else { spans[0].style.transform = ''; spans[1].style.opacity = ''; spans[2].style.transform = ''; document.body.style.overflow = ''; if (lastFocus) { lastFocus.focus(); lastFocus = null; } }
                });
                document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && isOpen) { e.preventDefault(); closeMenu(); } });
                links.forEach(function(link) { link.addEventListener('click', function() { closeMenu(); }); });
            }

            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) { if (entry.isIntersecting) { entry.target.classList.add('revealed'); observer.unobserve(entry.target); } });
                }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });
                document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .stagger-children').forEach(function(el) { observer.observe(el); });
            } else {
                document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .stagger-children').forEach(function(el) { el.classList.add('revealed'); });
            }

            var header = document.querySelector('header');
            if (header) {
                var headerObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) { header.style.borderBottomColor = !entry.isIntersecting ? 'rgba(0,0,0,0.06)' : ''; });
                }, { threshold: 0 });
                headerObserver.observe(document.querySelector('main'));
            }
        })();
    </script>
</body>
</html>
