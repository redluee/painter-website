<section class="py-24 md:py-32">
    <div class="mx-auto max-w-[1400px] px-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-24 items-start">
            <div class="lg:col-span-5 reveal-left">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent-1 mb-3">Kennismaken</p>
                <h1 class="text-4xl md:text-6xl font-bold tracking-tighter leading-[0.95] mb-8">Over mij</h1>
                <div class="aspect-[3/4] overflow-hidden bg-neutral-100">
                    <img src="<?= escapeHtml($profileImage) ?>" alt="Sebastiaan Peters" class="w-full h-full object-cover" loading="eager">
                </div>
            </div>
            <div class="lg:col-span-7 reveal-right">
                <div class="prose max-w-none text-neutral-600 leading-relaxed space-y-5 [&>p]:text-base [&>p]:leading-relaxed"><?= $aboutMe ?></div>
            </div>
        </div>
    </div>
</section>
