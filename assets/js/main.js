(function() {
'use strict';
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
link.addEventListener('click', function(e) {
var parentLi = link.parentElement;
var isMobile = window.matchMedia('(max-width: 960px)').matches;
var hasChildren = parentLi && parentLi.classList.contains('menu-item-has-children');
if (isMobile && hasChildren && !parentLi.classList.contains('submenu-open')) {
e.preventDefault();
var siblings = parentLi.parentElement.querySelectorAll(':scope > li.submenu-open');
siblings.forEach(function(s) { if (s !== parentLi) s.classList.remove('submenu-open'); });
parentLi.classList.add('submenu-open');
return;
}
menu.classList.remove('active');
document.body.classList.remove('overflow-hidden');
toggle.classList.remove('active');
toggle.setAttribute('aria-expanded', 'false');
menu.querySelectorAll('.submenu-open').forEach(function(el) { el.classList.remove('submenu-open'); });
});
});
}
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
var header = document.getElementById('site-header');
if ('IntersectionObserver' in window) {
var revealObs = new IntersectionObserver(function(entries) {
entries.forEach(function(entry) {
if (!entry.isIntersecting) return;
var el = entry.target;
var children = el.querySelectorAll('.card, .faq-item, .stat, .testimonial-card, .team-member');
if (children.length > 1) {
children.forEach(function(child, i) {
child.style.transitionDelay = (i * 0.1) + 's';
child.classList.add('reveal-child');
});
requestAnimationFrame(function() {
el.classList.add('visible');
});
} else {
el.classList.add('visible');
}
revealObs.unobserve(el);
});
}, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .image-reveal').forEach(function(el) {
revealObs.observe(el);
});
setTimeout(function() {
document.querySelectorAll('.reveal:not(.visible), .reveal-left:not(.visible), .reveal-right:not(.visible), .reveal-scale:not(.visible), .image-reveal:not(.visible)').forEach(function(el) {
el.classList.add('visible');
});
}, 2500);
}
function animateCounter(el) {
var text = el.textContent.trim();
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
el.textContent = text;
}
}
el.textContent = prefix + '0' + suffix;
requestAnimationFrame(step);
}
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
var parallaxEls = document.querySelectorAll('.hero, .page-hero, .cta-banner');
var enableParallax = parallaxEls.length && !prefersReducedMotion;
function applyParallax(scrollY) {
parallaxEls.forEach(function(el) {
var rect = el.getBoundingClientRect();
var elTop = rect.top + scrollY;
var elHeight = rect.height;
if (scrollY + window.innerHeight > elTop && scrollY < elTop + elHeight) {
var offset = (scrollY - elTop) * 0.15;
var img = el.querySelector('.hero-bg-img');
if (img) {
img.style.transform = 'translateY(' + offset + 'px) scale(1.1)';
}
}
});
}
if (enableParallax) applyParallax(window.scrollY);
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
var backToTop = document.querySelector('.back-to-top');
if (backToTop) {
backToTop.addEventListener('click', function(e) {
e.preventDefault();
window.scrollTo({ top: 0, behavior: 'smooth' });
});
}
var scrollTicking = false;
window.addEventListener('scroll', function() {
if (!scrollTicking) {
requestAnimationFrame(function() {
var scrollY = window.scrollY;
if (header) header.classList.toggle('scrolled', scrollY > 50);
if (backToTop) backToTop.classList.toggle('visible', scrollY > 400);
if (enableParallax) applyParallax(scrollY);
scrollTicking = false;
});
scrollTicking = true;
}
}, { passive: true });
if (!prefersReducedMotion && window.matchMedia('(hover: hover)').matches && window.innerWidth > 960) {
var glowEl = document.createElement('div');
glowEl.className = 'cursor-glow';
glowEl.setAttribute('aria-hidden', 'true');
document.body.appendChild(glowEl);
document.addEventListener('mousemove', function(e) {
document.documentElement.style.setProperty('--cursor-x', e.clientX + 'px');
document.documentElement.style.setProperty('--cursor-y', e.clientY + 'px');
}, { passive: true });
}
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