<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Website kleuren</h2>
    <form id="settings-form" class="space-y-8">
        <fieldset>
            <legend class="text-lg font-semibold text-gray-900 mb-4">Accentkleuren</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="accent1" class="block text-sm font-medium text-gray-700 mb-2">Accent 1 (hoofdkleur)</label>
                    <div class="flex items-center gap-3">
                        <input type="color" id="accent1" required class="w-12 h-12 border border-gray-300 cursor-pointer">
                        <input type="text" id="accent1-hex" required class="flex-1 px-4 py-2.5 border border-gray-300 text-sm font-mono uppercase" placeholder="#1C2B1E" maxlength="7">
                    </div>
                </div>
                <div>
                    <label for="accent2" class="block text-sm font-medium text-gray-700 mb-2">Accent 2 (hover)</label>
                    <div class="flex items-center gap-3">
                        <input type="color" id="accent2" required class="w-12 h-12 border border-gray-300 cursor-pointer">
                        <input type="text" id="accent2-hex" required class="flex-1 px-4 py-2.5 border border-gray-300 text-sm font-mono uppercase" placeholder="#2A3D2C" maxlength="7">
                    </div>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend class="text-lg font-semibold text-gray-900 mb-4">Achtergrondkleuren</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="sectionBg" class="block text-sm font-medium text-gray-700 mb-2">Achtergrond secties</label>
                    <div class="flex items-center gap-3">
                        <input type="color" id="sectionBg" required class="w-12 h-12 border border-gray-300 cursor-pointer">
                        <input type="text" id="sectionBg-hex" required class="flex-1 px-4 py-2.5 border border-gray-300 text-sm font-mono uppercase" placeholder="#F0F5EE" maxlength="7">
                    </div>
                </div>
                <div>
                    <label for="navbarBg" class="block text-sm font-medium text-gray-700 mb-2">Achtergrond navigatiebalk</label>
                    <div class="flex items-center gap-3">
                        <input type="color" id="navbarBg" required class="w-12 h-12 border border-gray-300 cursor-pointer">
                        <input type="text" id="navbarBg-hex" required class="flex-1 px-4 py-2.5 border border-gray-300 text-sm font-mono uppercase" placeholder="#F0F5EE" maxlength="7">
                    </div>
                </div>
            </div>
        </fieldset>
        <p id="error-message" class="text-sm text-red-600 hidden" role="alert"></p>
        <p id="success-message" class="text-sm text-green-600 hidden" role="status"></p>
        <div class="flex gap-3">
            <button type="submit" class="px-8 py-3 bg-accent-1 text-white text-sm font-medium rounded-xl hover:bg-accent-2 transition-colors">Opslaan</button>
            <a href="/admin/dashboard" class="px-8 py-3 border border-gray-300 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">Annuleren</a>
        </div>
    </form>
</div>

<script>
    var form = document.getElementById('settings-form');
    var errorEl = document.getElementById('error-message');
    var successEl = document.getElementById('success-message');

    var colorInputs = [
        { color: document.getElementById('accent1'), hex: document.getElementById('accent1-hex') },
        { color: document.getElementById('accent2'), hex: document.getElementById('accent2-hex') },
        { color: document.getElementById('sectionBg'), hex: document.getElementById('sectionBg-hex') },
        { color: document.getElementById('navbarBg'), hex: document.getElementById('navbarBg-hex') },
    ];

    function showError(msg) { errorEl.textContent = msg; errorEl.classList.remove('hidden'); successEl.classList.add('hidden'); }
    function showSuccess(msg) { successEl.textContent = msg; successEl.classList.remove('hidden'); errorEl.classList.add('hidden'); }
    function hideMessages() { errorEl.classList.add('hidden'); successEl.classList.add('hidden'); }

    colorInputs.forEach(function (pair) {
        pair.color.addEventListener('input', function () { pair.hex.value = pair.color.value; });
        pair.hex.addEventListener('input', function () {
            var val = pair.hex.value.trim();
            if (/^#[0-9a-fA-F]{6}$/.test(val)) { pair.color.value = val.toLowerCase(); }
        });
    });

    async function loadSettings() {
        try {
            var res = await fetch('/api/admin/settings');
            if (!res.ok) throw new Error('Laden mislukt');
            var data = await res.json();
            colorInputs.forEach(function (pair) {
                var id = pair.color.id;
                var value = data[id];
                if (value) { pair.color.value = value; pair.hex.value = value.toUpperCase(); }
            });
        } catch { showError('Kon instellingen niet laden.'); }
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        hideMessages();
        var hexRegex = /^#[0-9a-fA-F]{6}$/;
        var payload = {};
        for (var i = 0; i < colorInputs.length; i++) {
            var val = colorInputs[i].hex.value.trim();
            if (!hexRegex.test(val)) { showError('"' + val + '" is geen geldige hex-kleur. Gebruik formaat #RRGGBB.'); return; }
            payload[colorInputs[i].hex.id.replace('-hex', '')] = val;
        }
        try {
            var res = await fetch('/api/admin/settings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            if (res.ok) {
                showSuccess('Kleuren opgeslagen!');
                var link = document.querySelector('link[href="/api/theme.css"]');
                if (link) {
                    var newLink = document.createElement('link');
                    newLink.rel = 'stylesheet';
                    newLink.href = '/api/theme.css?' + Date.now();
                    link.parentNode.insertBefore(newLink, link.nextSibling);
                    setTimeout(function () { link.remove(); }, 500);
                }
                colorInputs.forEach(function (pair) { pair.color.value = pair.hex.value.trim().toLowerCase(); });
            } else { var data = await res.json(); showError(data.error || 'Opslaan mislukt'); }
        } catch { showError('Er is iets misgegaan.'); }
    });

    loadSettings();
</script>
