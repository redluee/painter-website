<?php $size = $size ?? 20; $stars = $project['review']['stars'] ?? 0; ?>
<div class="inline-flex gap-0.5">
    <?php for ($i = 1; $i <= 5; $i++): ?>
    <svg xmlns="http://www.w3.org/2000/svg" width="<?= $size ?>" height="<?= $size ?>" viewBox="0 0 24 24" class="text-amber-400">
        <defs>
            <clipPath id="star-clip-<?= $i ?>">
                <rect x="0" y="0" width="<?= min(24, max(0, ($stars - ($i - 1)) * 24)) ?>" height="24"/>
            </clipPath>
        </defs>
        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="none" stroke="currentColor" stroke-width="2"/>
        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="currentColor" clip-path="url(#star-clip-<?= $i ?>)"/>
    </svg>
    <?php endfor; ?>
</div>
