(function() {
    'use strict';

    // ========== Language switcher toggle ==========
    document.addEventListener('click', function(e) {
        var toggle = e.target.closest('.yvl-switcher__toggle');
        if (toggle) {
            var expanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', !expanded);
            return;
        }

        // Close on outside click
        if (!e.target.closest('.yvl-switcher--floating')) {
            var toggles = document.querySelectorAll('.yvl-switcher__toggle');
            toggles.forEach(function(t) { t.setAttribute('aria-expanded', 'false'); });
        }
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var toggles = document.querySelectorAll('.yvl-switcher__toggle');
            toggles.forEach(function(t) { t.setAttribute('aria-expanded', 'false'); });
        }
    });
})();
