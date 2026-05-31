<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Project bewerken</h2>
    <form id="project-form" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Projectnaam</label>
            <input type="text" id="name" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-accent-1 focus:border-transparent">
        </div>
        <div>
            <label for="paintType" class="block text-sm font-medium text-gray-700 mb-1">Type verfwerk (komma-gescheiden)</label>
            <input type="text" id="paintType" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-accent-1 focus:border-transparent" placeholder="Bijv. buiten, historisch">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Beschrijving</label>
            <p class="text-xs text-gray-400 mb-2">Gebruik <strong>Ctrl+B</strong> voor vet, <strong>Ctrl+I</strong> voor cursief.</p>
            <div id="description-editor" class="bg-white border border-gray-300 rounded-xl overflow-hidden quill-custom"></div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Review (optioneel)</label>
            <p class="text-xs text-gray-400 mb-2">Beoordeling van de klant.</p>
            <div class="flex gap-1 mb-3 select-none" id="review-stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="star-unit relative w-8 h-8 cursor-pointer" data-star="<?= $i ?>" aria-label="<?= $i ?> ster">
                    <svg class="star-empty absolute inset-0 w-full h-full text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg class="star-fill absolute inset-0 w-full h-full text-amber-400" fill="currentColor" stroke="none" viewBox="0 0 24 24" style="clip-path:inset(0 100% 0 0)"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <?php endfor; ?>
            </div>
            <input type="hidden" id="review-stars-value" value="0">
            <textarea id="review-description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-accent-1 focus:border-transparent" placeholder="Wat vond de klant van het werk?"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Afbeeldingen</label>
            <div id="existing-images" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4"></div>
            <div id="dropzone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-gray-400 transition-colors">
                <p class="text-sm text-gray-500 mb-1">Sleep nieuwe afbeeldingen hierheen of klik om te uploaden</p>
                <p class="text-xs text-gray-400">Alle afbeeldingen (worden automatisch gecomprimeerd)</p>
            </div>
            <input type="file" id="file-input" accept="image/*,.heic,.heif" multiple class="hidden">
        </div>
        <p id="error-message" class="text-sm text-red-600 hidden" role="alert"></p>
        <p id="success-message" class="text-sm text-green-600 hidden" role="status"></p>
        <div class="flex gap-3">
            <button type="submit" class="px-8 py-3 bg-accent-1 text-white text-sm font-medium rounded-xl hover:bg-accent-2 transition-colors">Opslaan</button>
            <a href="/admin/projects" class="px-8 py-3 border border-gray-300 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">Annuleren</a>
        </div>
    </form>
</div>

<style>
    .quill-custom .ql-editor { font-family: inherit; font-size: 0.875rem; line-height: 1.625; min-height: 150px; }
    .quill-custom .ql-toolbar { border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem; border-bottom: 1px solid #e5e7eb; }
    .quill-custom .ql-container { border: none; font-family: inherit; }
</style>

<script src="/assets/quill/quill.js"></script>
<link rel="stylesheet" href="/assets/quill/quill.snow.css">
<script src="/assets/client-image-utils.js"></script>
<script>
    var form = document.getElementById('project-form');
    var errorEl = document.getElementById('error-message');
    var successEl = document.getElementById('success-message');
    var dropzone = document.getElementById('dropzone');
    var fileInput = document.getElementById('file-input');
    var existingImages = document.getElementById('existing-images');
    var reviewStarsInput = document.getElementById('review-stars-value');
    var currentPictures = [];
    var draggedUrl = null;
    var selectedStars = 0;
    var processingEntries = new Map();
    var compressedSizes = new Map();
    var slug = decodeURIComponent(window.location.pathname.split('/').pop());

    function updateStars(value) {
        document.querySelectorAll('#review-stars .star-unit').forEach(function (el, i) {
            var pct = Math.min(1, Math.max(0, value - i)) * 100;
            el.querySelector('.star-fill').style.clipPath = 'inset(0 ' + (100 - pct) + '% 0 0)';
        });
    }
    function setStars(value) { selectedStars = value; reviewStarsInput.value = value; updateStars(value); }

    var starsContainer = document.getElementById('review-stars');
    starsContainer.addEventListener('mousemove', function (e) {
        var unit = e.target.closest('.star-unit'); if (!unit) return;
        var pos = parseInt(unit.dataset.star); var rect = unit.getBoundingClientRect();
        var isLeft = (e.clientX - rect.left) < rect.width / 2;
        updateStars(isLeft ? pos - 0.5 : pos);
    });
    starsContainer.addEventListener('mouseleave', function () { updateStars(selectedStars); });
    starsContainer.addEventListener('click', function (e) {
        var unit = e.target.closest('.star-unit'); if (!unit) return;
        var pos = parseInt(unit.dataset.star); var rect = unit.getBoundingClientRect();
        var isLeft = (e.clientX - rect.left) < rect.width / 2;
        setStars(isLeft ? pos - 0.5 : pos);
    });

    var descriptionEditor;
    var quillReady = false;

    function initQuill() {
        if (typeof Quill === 'undefined') return;
        quillReady = true;
        descriptionEditor = new Quill('#description-editor', { theme: 'snow', modules: { toolbar: [['bold', 'italic'], [{ list: 'bullet' }], ['clean']] } });
        if (window._cachedProject) setProjectContent(window._cachedProject);
    }

    function setProjectContent(project) {
        if (!quillReady) return;
        descriptionEditor.root.innerHTML = project.description || '';
    }

    function showError(msg) { errorEl.textContent = msg; errorEl.classList.remove('hidden'); successEl.classList.add('hidden'); }
    function showSuccess(msg) { successEl.textContent = msg; successEl.classList.remove('hidden'); errorEl.classList.add('hidden'); }
    function hideMessages() { errorEl.classList.add('hidden'); successEl.classList.add('hidden'); }

    function renderExistingImages() {
        var html = '';
        processingEntries.forEach(function (entry, id) {
            html += '<div class="relative aspect-[4/3] rounded-lg overflow-hidden bg-gray-50 border-2 border-accent-1/20 flex flex-col items-center justify-center gap-1.5 p-3">' +
                '<svg class="animate-spin h-5 w-5 text-accent-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>' +
                '<span class="text-xs text-gray-500 text-center leading-tight truncate w-full">' + entry.fileName + '</span>' +
                '<span class="text-[11px] text-accent-1 font-medium">' + entry.status + '...</span>' +
                '<span class="text-[10px] text-gray-400">' + ImageUtils.formatFileSize(entry.originalSize) + '</span></div>';
        });
        currentPictures.forEach(function (url, i) {
            var badge = i === 0 ? '<span class="absolute top-2 left-2 z-10 px-2 py-0.5 bg-accent-1/70 text-white text-xs rounded pointer-events-none">Primair</span>' : '';
            var size = compressedSizes.get(url);
            if (size !== undefined) badge = '<span class="absolute top-2 left-2 z-10 px-1.5 py-0.5 bg-green-600/80 text-white text-[10px] font-medium rounded pointer-events-none">' + ImageUtils.formatFileSize(size) + '</span>';
            html += '<div class="relative group aspect-[4/3] rounded-lg overflow-hidden bg-gray-100 cursor-grab" draggable="true" data-url="' + url.replace(/"/g, '&quot;') + '">' + badge +
                '<img src="' + url.replace(/"/g, '&quot;') + '" class="w-full h-full object-cover pointer-events-none" alt="">' +
                '<button type="button" data-url="' + url.replace(/"/g, '&quot;') + '" class="delete-img absolute top-2 right-2 w-7 h-7 bg-red-600 text-white rounded-full text-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700 z-10">&times;</button></div>';
        });
        existingImages.innerHTML = html;
        existingImages.querySelectorAll('.delete-img').forEach(function (btn) {
            btn.addEventListener('click', function () {
                currentPictures = currentPictures.filter(function (u) { return u !== btn.dataset.url; });
                compressedSizes.delete(btn.dataset.url); renderExistingImages();
            });
        });
    }

    existingImages.addEventListener('dragstart', function (e) {
        var item = e.target.closest('[draggable]'); if (!item) return;
        draggedUrl = item.dataset.url; e.dataTransfer.effectAllowed = 'move'; item.classList.add('opacity-50');
    });
    existingImages.addEventListener('dragover', function (e) {
        e.preventDefault(); e.dataTransfer.dropEffect = 'move';
        var item = e.target.closest('[draggable]'); if (item && item.dataset.url !== draggedUrl) item.classList.add('ring-2', 'ring-accent-1');
    });
    existingImages.addEventListener('dragleave', function (e) {
        var item = e.target.closest('[draggable]'); if (item) item.classList.remove('ring-2', 'ring-accent-1');
    });
    existingImages.addEventListener('drop', function (e) {
        e.preventDefault(); var target = e.target.closest('[draggable]');
        if (!target || !draggedUrl || target.dataset.url === draggedUrl) return;
        var fromIndex = currentPictures.indexOf(draggedUrl), toIndex = currentPictures.indexOf(target.dataset.url);
        if (fromIndex === -1 || toIndex === -1) return;
        var moved = currentPictures.splice(fromIndex, 1)[0]; currentPictures.splice(toIndex, 0, moved);
        renderExistingImages(); draggedUrl = null;
    });
    existingImages.addEventListener('dragend', function () {
        draggedUrl = null; existingImages.querySelectorAll('[draggable]').forEach(function (el) { el.classList.remove('opacity-50', 'ring-2', 'ring-accent-1'); });
    });

    dropzone.addEventListener('click', function () { fileInput.click(); });
    dropzone.addEventListener('dragover', function (e) { e.preventDefault(); dropzone.classList.add('border-accent-1'); });
    dropzone.addEventListener('dragleave', function () { dropzone.classList.remove('border-accent-1'); });
    dropzone.addEventListener('drop', function (e) { e.preventDefault(); dropzone.classList.remove('border-accent-1'); handleFiles(e.dataTransfer.files); });
    fileInput.addEventListener('change', function () { handleFiles(fileInput.files); });

    async function handleFiles(files) {
        var successCount = 0, errorCount = 0, lastError = '', hasProcessing = false;
        for (var i = 0; i < files.length; i++) {
            var file = files[i]; hasProcessing = true;
            var processId = 'proc-' + Date.now() + '-' + Math.random().toString(36).slice(2, 8);
            processingEntries.set(processId, { fileName: file.name, originalSize: file.size, status: 'comprimeren' });
            renderExistingImages();
            try {
                var result = await ImageUtils.compressImage(file);
                processingEntries.set(processId, { fileName: file.name, originalSize: file.size, status: 'uploaden' });
                renderExistingImages();
                var formData = new FormData(); formData.append('image', result.blob, file.name.replace(/\.[^.]+$/, '.webp'));
                var res = await fetch('/api/admin/upload', { method: 'POST', body: formData });
                if (res.ok) {
                    var data = await res.json(); currentPictures.push(data.url);
                    compressedSizes.set(data.url, result.compressedSize);
                    processingEntries.delete(processId); renderExistingImages(); successCount++;
                } else {
                    var err = await res.json(); lastError = err.error; processingEntries.delete(processId); renderExistingImages(); errorCount++;
                }
            } catch (e) {
                console.error('Upload error:', e); lastError = e instanceof Error ? e.message : 'Onbekende fout';
                processingEntries.delete(processId); renderExistingImages(); errorCount++;
            }
        }
        if (errorCount > 0) { showError(errorCount + ' bestand(en) mislukt. ' + (lastError ? 'Laatste fout: ' + lastError : '')); }
        else if (successCount > 0 || hasProcessing) { hideMessages(); }
        else { showError('Geen geldige afbeeldingen geselecteerd.'); }
    }

    async function loadProject() {
        try {
            var res = await fetch('/api/admin/projects/' + slug);
            if (!res.ok) throw new Error('Not found');
            var project = await res.json();
            document.getElementById('name').value = project.name;
            document.getElementById('paintType').value = (project.paintType || []).join(', ');
            window._cachedProject = project;
            setProjectContent(project);
            currentPictures = project.pictures || [];
            renderExistingImages();
            if (project.review && project.review.stars >= 1 && project.review.stars <= 5) {
                setStars(project.review.stars); document.getElementById('review-description').value = project.review.description || '';
            }
        } catch { showError('Project niet gevonden.'); }
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault(); hideMessages();
        var paintTypeRaw = document.getElementById('paintType').value;
        var paintType = paintTypeRaw ? paintTypeRaw.split(',').map(function (s) { return s.trim(); }).filter(Boolean) : [];
        if (quillReady && !descriptionEditor.getText().trim()) { showError('Het veld "Beschrijving" is verplicht.'); return; }
        var reviewDesc = document.getElementById('review-description').value.trim();
        var payload = {
            name: document.getElementById('name').value, paintType: paintType,
            description: descriptionEditor ? descriptionEditor.root.innerHTML : '', pictures: currentPictures,
            review: selectedStars > 0 ? { stars: selectedStars, description: reviewDesc } : null,
        };
        try {
            var res = await fetch('/api/admin/projects/' + slug, { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
            if (res.ok) { var updated = await res.json(); window.location.href = '/admin/projects/edit/' + updated.slug; }
            else { var data = await res.json(); showError(data.error || 'Opslaan mislukt'); }
        } catch { showError('Er is iets misgegaan.'); }
    });

    loadProject();
    initQuill();
</script>
