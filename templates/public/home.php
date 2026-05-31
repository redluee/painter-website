<!-- ── Asymmetric Hero ── -->
<section class="min-h-[100dvh] flex items-center relative overflow-hidden">
    <div class="mx-auto max-w-[1400px] px-6 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            <div class="lg:col-span-6 reveal-left">
                <div class="max-w-xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent-1 mb-6">Binnen- & buitenschilderwerk</p>
                    <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold tracking-tighter leading-[0.9] text-balance">
                        Vakwerk<br>
                        <span class="relative inline-block">
                            <img src="/brushstroke.svg" alt="" aria-hidden="true"
                                class="absolute left-1/2 top-9/10 -translate-x-2/3 -translate-y-6/11 w-[300%] md:w-[150%] h-auto max-w-none -z-10 pointer-events-none select-none md:top-[0.45em] md:-translate-y-4/10 md:-translate-x-1/2"
                                loading="eager">
                            <span class="text-white">met oog</span><br>
                            <span class="text-white md:text-current">voor detail</span>
                        </span>
                    </h1>
                    <p class="text-base md:text-lg text-neutral-500 leading-relaxed mt-8 max-w-md"><?= escapeHtml($businessInfo['intro'] ?? '') ?></p>
                    <div class="flex flex-col sm:flex-row gap-4 mt-10">
                        <a href="/contact" class="inline-flex items-center justify-center px-8 py-3.5 bg-accent-1 text-white text-sm font-medium rounded-full hover:bg-accent-2 active:scale-[0.97] transition-all duration-300 ease-spring">
                            Offerte aanvragen
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ml-2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                        <a href="/projecten" class="inline-flex items-center justify-center px-8 py-3.5 border border-neutral-200 text-neutral-700 text-sm font-medium rounded-full hover:border-accent-1 hover:text-accent-1 active:scale-[0.97] transition-all duration-300 ease-spring">
                            Bekijk projecten
                        </a>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-6 reveal-right">
                <?php if ($heroProject): ?>
                <div class="relative">
                    <div class="aspect-[4/5] w-full overflow-hidden">
                        <img src="<?= $heroProject['pictures'][0] ?? $DEFAULT_IMAGE ?>" alt="<?= escapeHtml($heroProject['name']) ?>" class="w-full h-full object-cover" loading="eager">
                    </div>
                    <div class="absolute -bottom-4 -left-4 bg-white/90 backdrop-blur-sm border border-neutral-100 px-6 py-4">
                        <p class="text-xs text-neutral-500 uppercase tracking-widest mb-1">Uitgelicht</p>
                        <p class="text-sm font-semibold"><?= escapeHtml($heroProject['name']) ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if ($recentProjects): ?>
<!-- ── Recent Projects ── -->
<section class="py-24 md:py-32">
    <div class="mx-auto max-w-[1400px] px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-4">
            <div class="reveal">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent-1 mb-3">Portfolio</p>
                <h2 class="text-3xl md:text-5xl font-bold tracking-tight">Recente projecten</h2>
            </div>
            <a href="/projecten" class="group inline-flex items-center gap-2 text-sm font-medium text-accent-1 reveal">
                Alle projecten
                <span class="inline-block transition-transform duration-300 group-hover:translate-x-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </span>
            </a>
        </div>
        <div class="stagger-children grid grid-cols-1 md:grid-cols-12 gap-6">
            <?php foreach ($recentProjects as $i => $project): $isFirst = $i === 0; ?>
            <a href="/projecten/<?= $project['slug'] ?>" class="stagger-item group block col-span-1 <?= $isFirst ? 'md:col-span-7 md:row-span-2' : 'md:col-span-5' ?>">
                <div class="relative overflow-hidden bg-neutral-100 <?= $isFirst ? 'aspect-[3/4]' : 'aspect-[4/3]' ?>">
                    <img src="<?= $project['pictures'][0] ?? $DEFAULT_IMAGE ?>" alt="<?= escapeHtml($project['name']) ?>"
                        class="w-full h-full object-cover transition-transform duration-700 ease-spring group-hover:scale-105"
                        loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                </div>
                <div class="mt-5">
                    <p class="text-xs text-neutral-400 uppercase tracking-widest mb-1.5"><?= escapeHtml($project['paintType'][0] ?? 'Schilderwerk') ?></p>
                    <h3 class="text-lg font-semibold tracking-tight group-hover:text-accent-1 transition-colors"><?= escapeHtml($project['name']) ?></h3>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── Expertise ── -->
<section class="py-24 md:py-32 bg-section-bg">
    <div class="mx-auto max-w-[1400px] px-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            <div class="lg:col-span-5 reveal-left">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent-1 mb-3">Waarom kiezen voor</p>
                <h2 class="text-3xl md:text-5xl font-bold tracking-tight mb-8">Vakmanschap dat verschil maakt</h2>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <span class="flex-shrink-0 w-8 h-8 rounded-full bg-accent-1/10 flex items-center justify-center mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-accent-1"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <div><p class="font-semibold mb-1">15+ jaar ervaring</p><p class="text-sm text-neutral-500 leading-relaxed">Ruime expertise in binnen- en buitenschilderwerk in de regio Utrecht.</p></div>
                    </div>
                    <div class="flex gap-4">
                        <span class="flex-shrink-0 w-8 h-8 rounded-full bg-accent-1/10 flex items-center justify-center mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-accent-1"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <div><p class="font-semibold mb-1">Persoonlijke aanpak</p><p class="text-sm text-neutral-500 leading-relaxed">Direct contact met de uitvoerder zelf. Korte lijnen, eerlijk advies.</p></div>
                    </div>
                    <div class="flex gap-4">
                        <span class="flex-shrink-0 w-8 h-8 rounded-full bg-accent-1/10 flex items-center justify-center mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-accent-1"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <div><p class="font-semibold mb-1">Kwaliteitsmaterialen</p><p class="text-sm text-neutral-500 leading-relaxed">Uitsluitend topmerken voor een duurzaam resultaat dat jaren meegaat.</p></div>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-7 reveal-right">
                <div class="grid grid-cols-2 gap-4">
                    <div class="aspect-[3/4] overflow-hidden">
                        <img src="<?= ($recentProjects[1]['pictures'][0] ?? $heroProject['pictures'][0] ?? $DEFAULT_IMAGE) ?>" alt="" class="w-full h-full object-cover" loading="lazy">
                    </div>
                    <div class="aspect-square mt-16 overflow-hidden">
                        <img src="<?= ($recentProjects[2]['pictures'][0] ?? $recentProjects[0]['pictures'][0] ?? $DEFAULT_IMAGE) ?>" alt="" class="w-full h-full object-cover" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA ── -->
<section class="py-24 md:py-32">
    <div class="mx-auto max-w-[1400px] px-6 text-center reveal">
        <div class="max-w-2xl mx-auto">
            <h2 class="text-3xl md:text-5xl font-bold tracking-tight mb-6">Klaar om uw project te bespreken?</h2>
            <p class="text-neutral-500 leading-relaxed mb-10 max-w-md mx-auto">Vraag een vrijblijvende offerte aan en ik kom langs voor een persoonlijk gesprek.</p>
            <a href="/contact" class="inline-flex items-center justify-center px-10 py-4 bg-accent-1 text-white text-sm font-medium rounded-full hover:bg-accent-2 active:scale-[0.97] transition-all duration-300 ease-spring">
                Neem contact op
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ml-2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>
</section>
