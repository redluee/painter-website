<section class="py-24 md:py-32">
    <div class="mx-auto max-w-[1400px] px-6">
        <div class="max-w-xl reveal">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent-1 mb-3">Portfolio</p>
            <h1 class="text-5xl md:text-7xl font-bold tracking-tighter leading-[0.9] mb-6">Projecten</h1>
            <p class="text-neutral-500 leading-relaxed">Een selectie van gerealiseerde projecten in de regio Utrecht.</p>
        </div>

        <?php if ($firstProject): ?>
        <a href="/projecten/<?= escapeHtml($firstProject['slug']) ?>" class="group block mt-16 reveal">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 overflow-hidden bg-neutral-100">
                    <div class="aspect-[16/10]">
                        <img src="<?= escapeHtml($firstProject['pictures'][0] ?? \App\Controllers\Public\ProjectsController::DEFAULT_IMAGE) ?>" alt="<?= escapeHtml($firstProject['name']) ?>" class="w-full h-full object-cover transition-transform duration-700 ease-spring group-hover:scale-105" loading="eager">
                    </div>
                </div>
                <div class="lg:col-span-5">
                    <p class="text-xs text-neutral-400 uppercase tracking-widest mb-3">Uitgelicht project</p>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-4 group-hover:text-accent-1 transition-colors"><?= escapeHtml($firstProject['name']) ?></h2>
                    <p class="text-sm text-neutral-500 leading-relaxed mb-6"><?= escapeHtml(implode(', ', $firstProject['paintType'] ?? [])) ?></p>
                    <span class="inline-flex items-center gap-2 text-sm font-medium text-accent-1">
                        Bekijk project
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </span>
                </div>
            </div>
        </a>
        <?php endif; ?>

        <?php if ($restProjects): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-20 mt-20 stagger-children">
            <?php foreach ($restProjects as $project): ?>
            <a href="/projecten/<?= escapeHtml($project['slug']) ?>" class="stagger-item group block">
                <div class="overflow-hidden bg-neutral-100">
                    <div class="aspect-[4/3]">
                        <img src="<?= escapeHtml($project['pictures'][0] ?? \App\Controllers\Public\ProjectsController::DEFAULT_IMAGE) ?>" alt="<?= escapeHtml($project['name']) ?>" class="w-full h-full object-cover transition-transform duration-700 ease-spring group-hover:scale-105" loading="lazy">
                    </div>
                </div>
                <div class="mt-5">
                    <p class="text-xs text-neutral-400 uppercase tracking-widest mb-1.5"><?= escapeHtml($project['paintType'][0] ?? 'Schilderwerk') ?></p>
                    <h2 class="text-xl font-semibold tracking-tight group-hover:text-accent-1 transition-colors"><?= escapeHtml($project['name']) ?></h2>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (empty($projects)): ?>
        <div class="mt-20 text-center py-20 border border-dashed border-neutral-200">
            <p class="text-neutral-400">Nog geen projecten toegevoegd.</p>
        </div>
        <?php endif; ?>
    </div>
</section>
