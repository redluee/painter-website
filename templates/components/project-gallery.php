<?php $pictures = $project['pictures'] ?? []; $projectName = $project['name'] ?? ''; ?>
<?php if ($pictures): ?>
<div class="project-gallery">
    <div class="relative overflow-hidden bg-neutral-100 cursor-pointer" id="gallery-hero">
        <div class="aspect-[16/10] md:aspect-[16/9]">
            <img src="<?= escapeHtml($pictures[0]) ?>" alt="<?= escapeHtml($projectName) ?>" class="w-full h-full object-cover" id="gallery-hero-img">
        </div>
        <button id="gallery-play-btn" class="absolute bottom-4 right-4 w-10 h-10 bg-white/80 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition-colors" aria-label="Slideshow starten">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
        </button>
    </div>
    <?php if (count($pictures) > 1): ?>
    <div class="flex gap-2 mt-2 overflow-x-auto" id="gallery-thumbnails">
        <?php foreach ($pictures as $i => $url): ?>
        <button class="flex-shrink-0 w-20 h-16 overflow-hidden border-2 transition-colors <?= $i === 0 ? 'border-accent-1' : 'border-transparent hover:border-gray-300' ?>" data-index="<?= $i ?>">
            <img src="<?= escapeHtml($url) ?>" alt="" class="w-full h-full object-cover" loading="lazy">
        </button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<div id="lightbox" class="hidden fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center">
    <button id="lightbox-close" class="absolute top-4 right-4 w-10 h-10 text-white/70 hover:text-white" aria-label="Sluiten">&times;</button>
    <button id="lightbox-prev" class="absolute left-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white text-3xl" aria-label="Vorige">&#8249;</button>
    <button id="lightbox-next" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white text-3xl" aria-label="Volgende">&#8250;</button>
    <img id="lightbox-img" src="" alt="" class="max-w-[90vw] max-h-[90vh] object-contain">
    <div class="absolute bottom-6 text-white/60 text-sm" id="lightbox-counter">1 / <?= count($pictures) ?></div>
</div>

<script>
    (function() {
        var pictures = <?= json_encode(array_map('escapeHtml', $pictures)) ?>;
        var currentIndex = 0;
        var autoplayInterval = null;
        var heroImg = document.getElementById('gallery-hero-img');
        var lightbox = document.getElementById('lightbox');
        var lightboxImg = document.getElementById('lightbox-img');
        var lightboxCounter = document.getElementById('lightbox-counter');
        var thumbnails = document.querySelectorAll('#gallery-thumbnails button');

        function showImage(index) {
            currentIndex = (index + pictures.length) % pictures.length;
            if (heroImg) heroImg.src = pictures[currentIndex];
            if (lightboxImg) lightboxImg.src = pictures[currentIndex];
            if (lightboxCounter) lightboxCounter.textContent = (currentIndex + 1) + ' / ' + pictures.length;
            thumbnails.forEach(function(t, i) { t.classList.toggle('border-accent-1', i === currentIndex); t.classList.toggle('border-transparent', i !== currentIndex); });
        }

        document.getElementById('gallery-hero')?.addEventListener('click', function() {
            lightbox.classList.remove('hidden'); lightboxImg.src = pictures[currentIndex]; lightboxCounter.textContent = (currentIndex + 1) + ' / ' + pictures.length;
            if (autoplayInterval) { clearInterval(autoplayInterval); autoplayInterval = null; }
        });

        document.getElementById('lightbox-close')?.addEventListener('click', function() { lightbox.classList.add('hidden'); });
        document.getElementById('lightbox-prev')?.addEventListener('click', function(e) { e.stopPropagation(); showImage(currentIndex - 1); });
        document.getElementById('lightbox-next')?.addEventListener('click', function(e) { e.stopPropagation(); showImage(currentIndex + 1); });
        lightbox?.addEventListener('click', function(e) { if (e.target === lightbox) lightbox.classList.add('hidden'); });

        document.addEventListener('keydown', function(e) {
            if (!lightbox.classList.contains('hidden')) {
                if (e.key === 'Escape') lightbox.classList.add('hidden');
                if (e.key === 'ArrowLeft') showImage(currentIndex - 1);
                if (e.key === 'ArrowRight') showImage(currentIndex + 1);
            }
        });

        thumbnails.forEach(function(t) {
            t.addEventListener('click', function() { showImage(parseInt(t.dataset.index)); });
        });

        document.getElementById('gallery-play-btn')?.addEventListener('click', function(e) {
            e.stopPropagation();
            if (autoplayInterval) { clearInterval(autoplayInterval); autoplayInterval = null; this.querySelector('polygon').setAttribute('points', '5 3 19 12 5 21 5 3'); return; }
            this.querySelector('polygon').setAttribute('points', '6 4 6 20 10 20 10 4 6 4 18 4 18 20 14 20 14 4 18 4');
            autoplayInterval = setInterval(function() { showImage(currentIndex + 1); }, 5000);
        });
    })();
</script>
<?php endif; ?>
