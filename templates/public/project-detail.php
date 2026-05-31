<?php $ogImage = $project['pictures'][0] ?? '/sebastiaan-profiel.jpg'; ?>
<article class="mx-auto max-w-[1400px] px-6 py-20 md:py-28">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://sebastiaanpeters.nl/" },
            { "@type": "ListItem", "position": 2, "name": "Projecten", "item": "https://sebastiaanpeters.nl/projecten" },
            { "@type": "ListItem", "position": 3, "name": "<?= escapeHtml($project['name']) ?>", "item": "https://sebastiaanpeters.nl/projecten/<?= escapeHtml($project['slug']) ?>" }
        ]
    }
    </script>

    <div class="max-w-5xl mx-auto">
        <header class="mb-16 reveal">
            <div class="flex items-center gap-3 text-xs text-neutral-400 uppercase tracking-widest mb-4">
                <a href="/projecten" class="hover:text-accent-1 transition-colors">Projecten</a>
                <span>/</span>
                <span class="text-accent-1"><?= escapeHtml($project['name']) ?></span>
            </div>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold tracking-tighter leading-[0.95] text-balance"><?= escapeHtml($project['name']) ?></h1>
            <?php if (!empty($project['paintType'])): ?>
            <div class="flex flex-wrap gap-2 mt-6">
                <?php foreach ($project['paintType'] as $type): ?>
                <span class="inline-block px-3 py-1.5 bg-section-bg text-xs font-medium text-accent-1 rounded-full"><?= escapeHtml($type) ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </header>

        <div class="reveal">
            <?php require dirname(__DIR__, 2) . '/templates/components/project-gallery.php'; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-12 mt-16">
            <div class="md:col-span-3 reveal">
                <div class="text-base leading-relaxed text-neutral-600 space-y-5 max-w-prose [&>p]:text-base [&>p]:leading-relaxed"><?= $project['description'] ?></div>
            </div>

            <aside class="md:col-span-2">
                <?php if (!empty($project['review'])): ?>
                <div class="bg-section-bg p-6 reveal">
                    <p class="text-xs font-semibold uppercase tracking-widest text-accent-1 mb-3">Beoordeling</p>
                    <div class="mb-3">
                        <?php require dirname(__DIR__, 2) . '/templates/components/star-rating.php'; ?>
                    </div>
                    <p class="text-sm text-neutral-600 italic">&ldquo;<?= escapeHtml($project['review']['description']) ?>&rdquo;</p>
                </div>
                <?php endif; ?>

                <div class="border border-neutral-200 p-6 mt-6 reveal">
                    <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400 mb-3">Projectinformatie</p>
                    <div class="space-y-3 text-sm text-neutral-600">
                        <div class="flex justify-between">
                            <span class="text-neutral-400">Verfsoort</span>
                            <span class="font-medium text-right"><?= escapeHtml(implode(', ', $project['paintType'] ?? [])) ?: 'Niet gespecificeerd' ?></span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        <footer class="mt-24 pt-16 border-t border-neutral-100 reveal">
            <div class="max-w-2xl">
                <h2 class="text-2xl md:text-3xl font-bold tracking-tight mb-4">Interesse in een soortgelijk project?</h2>
                <p class="text-neutral-500 mb-8 leading-relaxed">Ik denk graag met u mee over de mogelijkheden voor uw woning of bedrijfspand.</p>
                <a href="/contact" class="inline-flex items-center justify-center px-8 py-3.5 bg-accent-1 text-white text-sm font-medium rounded-full hover:bg-accent-2 active:scale-[0.97] transition-all duration-300 ease-spring">
                    Vraag een offerte aan
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ml-2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </footer>
    </div>
</article>
