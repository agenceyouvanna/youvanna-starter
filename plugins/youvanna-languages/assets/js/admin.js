(function() {
    'use strict';

    // ========== Collapsible post blocks ==========
    document.addEventListener('click', function(e) {
        var toggle = e.target.closest('.yvl-toggle');
        if (!toggle) return;

        var block = toggle.closest('.yvl-post-block');
        if (!block) return;

        var body = block.querySelector('.yvl-post-block__body');
        if (!body) return;

        var expanded = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', !expanded);
        body.style.display = expanded ? 'none' : 'block';
    });

    // ========== Translation progress counter ==========
    function updateProgress() {
        var inputs = document.querySelectorAll('.yvl-translated input[type="text"], .yvl-translated textarea');
        if (!inputs.length) return;

        var filled = 0;
        var total = inputs.length;

        inputs.forEach(function(input) {
            if (input.value.trim() !== '') filled++;
        });

        var el = document.getElementById('yvl-progress');
        if (el) {
            var pct = Math.round((filled / total) * 100);
            el.textContent = filled + '/' + total + ' champs traduits (' + pct + '%)';
        }
    }

    // Update on input
    document.addEventListener('input', function(e) {
        if (e.target.closest('.yvl-translated')) {
            updateProgress();
        }
    });

    // Initial count
    document.addEventListener('DOMContentLoaded', updateProgress);

    // ========== Expand all / collapse all ==========
    document.addEventListener('keydown', function(e) {
        // Ctrl+Shift+E = expand all, Ctrl+Shift+C = collapse all
        if (e.ctrlKey && e.shiftKey && e.key === 'E') {
            e.preventDefault();
            document.querySelectorAll('.yvl-toggle').forEach(function(t) {
                t.setAttribute('aria-expanded', 'true');
                var body = t.closest('.yvl-post-block').querySelector('.yvl-post-block__body');
                if (body) body.style.display = 'block';
            });
        }
        if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            document.querySelectorAll('.yvl-toggle').forEach(function(t) {
                t.setAttribute('aria-expanded', 'false');
                var body = t.closest('.yvl-post-block').querySelector('.yvl-post-block__body');
                if (body) body.style.display = 'none';
            });
        }
    });

})();
