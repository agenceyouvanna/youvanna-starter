(function() {
    'use strict';

    // ========== Mobile menu ==========
    var toggle = document.querySelector('.nav-toggle');
    var menu = document.querySelector('.nav-menu');
    if (toggle && menu) {
        toggle.addEventListener('click', function() {
            var expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            menu.classList.toggle('active');
            toggle.classList.toggle('active');
            document.body.classList.toggle('overflow-hidden');
        });
        menu.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                menu.classList.remove('active');
                document.body.classList.remove('overflow-hidden');
                toggle.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // ========== Reset mobile menu on resize to desktop ==========
    var mql960 = window.matchMedia('(min-width: 961px)');
    mql960.addEventListener('change', function(e) {
        if (e.matches && menu) {
            menu.classList.remove('active');
            document.body.classList.remove('overflow-hidden');
            if (toggle) {
                toggle.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
            }
        }
    });

    // ========== Header scroll ==========
    var header = document.getElementById('site-header');

    // ========== Scroll reveal with stagger ==========
    if ('IntersectionObserver' in window) {
        var revealObs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (!entry.isIntersecting) return;
                var el = entry.target;
                // Stagger children (cards in grids)
                var children = el.querySelectorAll('.card, .faq-item, .stat, .testimonial-card, .team-member');
                if (children.length > 1) {
                    children.forEach(function(child, i) {
                        child.style.transitionDelay = (i * 0.1) + 's';
                        child.classList.add('reveal-child');
                    });
                    // Trigger after small delay to let transition-delay apply
                    requestAnimationFrame(function() {
                        el.classList.add('visible');
                    });
                } else {
                    el.classList.add('visible');
                }
                revealObs.unobserve(el);
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

        document.querySelectorAll('.reveal').forEach(function(el) {
            revealObs.observe(el);
        });
    }

    // ========== Counter animation for numbers ==========
    function animateCounter(el) {
        var text = el.textContent.trim();
        // Parse: extract numeric part, prefix, suffix
        var match = text.match(/^([^\d]*)([\d]+(?:[.,]\d+)?)\s*(%|€|\+|ans|h|k|K|M)?(.*)$/);
        if (!match) return;
        var prefix = match[1] || '';
        var numStr = match[2].replace(',', '.');
        var target = parseFloat(numStr);
        var suffix = (match[3] || '') + (match[4] || '');
        var isDecimal = numStr.indexOf('.') !== -1;
        var decimals = isDecimal ? (numStr.split('.')[1] || '').length : 0;
        var duration = 2000;
        var startTime = null;

        function easeOutExpo(t) {
            return t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
        }

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var easedProgress = easeOutExpo(progress);
            var current = easedProgress * target;
            if (isDecimal) {
                el.textContent = prefix + current.toFixed(decimals).replace('.', ',') + suffix;
            } else {
                el.textContent = prefix + Math.floor(current) + suffix;
            }
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                // Restore exact original text
                el.textContent = text;
            }
        }

        el.textContent = prefix + '0' + suffix;
        requestAnimationFrame(step);
    }

    // Observe stat-numbers for counter animation
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if ('IntersectionObserver' in window && !prefersReducedMotion) {
        var counterObs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (!entry.isIntersecting) return;
                animateCounter(entry.target);
                counterObs.unobserve(entry.target);
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.stat-number').forEach(function(el) {
            counterObs.observe(el);
        });
    }

    // ========== Parallax on heroes and CTA ==========
    var parallaxEls = document.querySelectorAll('.hero, .page-hero, .cta-banner');
    var enableParallax = parallaxEls.length && !prefersReducedMotion;

    // ========== Smooth scroll for anchor links ==========
    document.querySelectorAll('a[href^="#"]').forEach(function(a) {
        a.addEventListener('click', function(e) {
            var href = this.getAttribute('href');
            if (!href || href === '#') return;
            var target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                if (menu) menu.classList.remove('active');
            }
        });
    });

    // ========== Back to top button ==========
    var backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ========== Consolidated scroll handler (header + parallax + back-to-top) ==========
    var scrollTicking = false;
    window.addEventListener('scroll', function() {
        if (!scrollTicking) {
            requestAnimationFrame(function() {
                var scrollY = window.scrollY;
                // Header scroll state
                if (header) header.classList.toggle('scrolled', scrollY > 50);
                // Back to top visibility
                if (backToTop) backToTop.classList.toggle('visible', scrollY > 400);
                // Parallax
                if (enableParallax) {
                    parallaxEls.forEach(function(el) {
                        var rect = el.getBoundingClientRect();
                        var elTop = rect.top + scrollY;
                        var elHeight = rect.height;
                        if (scrollY + window.innerHeight > elTop && scrollY < elTop + elHeight) {
                            var offset = (scrollY - elTop) * 0.3;
                            el.style.backgroundPositionY = 'calc(50% + ' + offset + 'px)';
                        }
                    });
                }
                scrollTicking = false;
            });
            scrollTicking = true;
        }
    }, { passive: true });

    // ========== FAQ smooth toggle ==========
    document.querySelectorAll('.faq-item summary').forEach(function(summary) {
        summary.addEventListener('click', function(e) {
            e.preventDefault();
            var details = this.parentElement;
            var content = details.querySelector('.faq-answer');
            if (details.hasAttribute('open')) {
                content.style.maxHeight = content.scrollHeight + 'px';
                requestAnimationFrame(function() {
                    content.style.maxHeight = '0px';
                    content.style.opacity = '0';
                });
                content.addEventListener('transitionend', function handler(e) {
                    if (e.propertyName !== 'max-height') return;
                    details.removeAttribute('open');
                    content.style.maxHeight = '';
                    content.style.opacity = '';
                    content.removeEventListener('transitionend', handler);
                });
            } else {
                details.setAttribute('open', '');
                var h = content.scrollHeight;
                content.style.maxHeight = '0px';
                content.style.opacity = '0';
                requestAnimationFrame(function() {
                    content.style.maxHeight = h + 'px';
                    content.style.opacity = '1';
                });
                content.addEventListener('transitionend', function handler(e) {
                    if (e.propertyName !== 'max-height') return;
                    content.style.maxHeight = '';
                    content.removeEventListener('transitionend', handler);
                });
            }
        });
    });
})();