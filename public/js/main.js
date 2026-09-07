/* ==========================================================================
   KIM DONG BOOKSTORE - Frontend effects layer
   ========================================================================== */
(function () {
    'use strict';

    var doc = document;
    var body = doc.body;
    doc.documentElement.classList.add('js');

    /* ---------- 1. Scroll reveal (IntersectionObserver) ---------- */
    function initReveal() {
        var items = doc.querySelectorAll('.reveal');
        if (!('IntersectionObserver' in window)) {
            items.forEach(function (el) { el.classList.add('reveal-visible'); });
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        items.forEach(function (el) { io.observe(el); });
    }

    /* ---------- 2. Sticky header shrink ---------- */
    function initStickyHeader() {
        var header = doc.querySelector('.site-header');
        if (!header) { return; }
        var onScroll = function () {
            if (window.scrollY > 60) {
                header.classList.add('sticky-header');
            } else {
                header.classList.remove('sticky-header');
            }
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* ---------- 3. Back to top ---------- */
    function initToTop() {
        var btn = doc.querySelector('.to-top');
        if (!btn) { return; }
        window.addEventListener('scroll', function () {
            btn.classList.toggle('visible', window.scrollY > 420);
        }, { passive: true });
        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ---------- 4. Button ripple ---------- */
    function initRipple() {
        if (!('PointerEvent' in window)) { return; }
        doc.addEventListener('pointerdown', function (e) {
            var btn = e.target.closest('.btn, .btn-cart-add, .pagination a, .to-top');
            if (!btn) { return; }
            var rect = btn.getBoundingClientRect();
            var d = Math.max(rect.width, rect.height) * 1.4;
            var ink = doc.createElement('span');
            ink.className = 'ripple-ink';
            ink.style.width = ink.style.height = d + 'px';
            ink.style.left = (e.clientX - rect.left - d / 2) + 'px';
            ink.style.top = (e.clientY - rect.top - d / 2) + 'px';
            btn.appendChild(ink);
            setTimeout(function () { ink.remove(); }, 600);
        });
    }

    /* ---------- 5. Quantity steppers ---------- */
    function initSteppers() {
        doc.querySelectorAll('.quantity-stepper').forEach(function (stepper) {
            var input = stepper.querySelector('input');
            if (!input) { return; }
            var minus = stepper.querySelector('.qty-btn.minus');
            var plus = stepper.querySelector('.qty-btn.plus');
            if (minus) {
                minus.addEventListener('click', function () {
                    var v = parseInt(input.value, 10) || 0;
                    var min = parseInt(input.min, 10) || 1;
                    if (v > min) {
                        input.value = v - 1;
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }
            if (plus) {
                plus.addEventListener('click', function () {
                    var v = parseInt(input.value, 10) || 0;
                    var max = parseInt(input.max, 10) || Infinity;
                    if (v < max) {
                        input.value = v + 1;
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }
        });
    }

    /* ---------- 6. Add-to-cart feedback ---------- */
    function initAddToCart() {
        doc.querySelectorAll('.btn-cart-add').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (btn.classList.contains('added')) { return; }
                btn.classList.add('added');
                var original = btn.innerHTML;
                btn.innerHTML = '✓ Đã thêm vào giỏ';
                setTimeout(function () {
                    btn.classList.remove('added');
                    btn.innerHTML = original;
                }, 1600);
            });
        });
    }

    /* ---------- 7. Animated counters ---------- */
    function initCounters() {
        var nums = doc.querySelectorAll('.counter');
        if (!nums.length) { return; }
        function animate(el) {
            var target = parseFloat(el.getAttribute('data-target')) || 0;
            var suffix = el.getAttribute('data-suffix') || '';
            var prefix = el.getAttribute('data-prefix') || '';
            var decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
            if (!('requestAnimationFrame' in window)) {
                el.textContent = prefix + target.toLocaleString('vi-VN', { minimumFractionDigits: decimals }) + suffix;
                return;
            }
            var duration = 1200;
            var start = null;
            function step(ts) {
                if (!start) { start = ts; }
                var prog = Math.min((ts - start) / duration, 1);
                var eased = 1 - Math.pow(1 - prog, 3);
                el.textContent = prefix + (target * eased).toLocaleString('vi-VN', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                }) + suffix;
                if (prog < 1) { requestAnimationFrame(step); }
            }
            requestAnimationFrame(step);
        }
        if (!('IntersectionObserver' in window)) {
            nums.forEach(animate);
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animate(entry.target);
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.4 });
        nums.forEach(function (el) { io.observe(el); });
    }

    /* ---------- 8. Animated chart bars ---------- */
    function initCharts() {
        var fills = doc.querySelectorAll('.chart-fill[data-width]');
        if (!fills.length) { return; }
        function fill(el) {
            el.style.width = el.getAttribute('data-width');
        }
        if (!('IntersectionObserver' in window)) {
            fills.forEach(fill);
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    fill(entry.target);
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.25 });
        fills.forEach(function (el) { io.observe(el); });
    }

    /* ---------- 9. Star bar fill animation ---------- */
    function initStarBars() {
        doc.querySelectorAll('.star-bar-fill[data-width]').forEach(function (bar) {
            setTimeout(function () {
                bar.style.width = bar.getAttribute('data-width');
            }, 350);
        });
    }

    /* ---------- 10. Mobile sidebar toggle ---------- */
    function initAdminSidebar() {
        var toggle = doc.querySelector('.sidebar-toggle');
        var sidebar = doc.querySelector('.admin-sidebar');
        if (!toggle || !sidebar) { return; }
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
        doc.addEventListener('click', function (e) {
            if (sidebar.classList.contains('open') &&
                !e.target.closest('.admin-sidebar') &&
                !e.target.closest('.sidebar-toggle')) {
                sidebar.classList.remove('open');
            }
        });
    }

    /* ---------- 11. Active nav highlight (admin) ---------- */
    function initActiveNav() {
        var path = (window.location.pathname || '').replace(/\/+$/, '');
        doc.querySelectorAll('.admin-nav a').forEach(function (a) {
            var href = (a.getAttribute('href') || '').split('?')[0].replace(/\/+$/, '');
            if (!href) { return; }
            if (href === '/admin') {
                if (path === '/admin') { a.classList.add('active'); }
            } else if (href.length > 1 && path.indexOf(href) === 0) {
                a.classList.add('active');
            }
        });
    }

    /* ---------- 12. Cart badge pop animation ---------- */
    function initCartBadge() {
        var badge = doc.querySelector('.cart-badge');
        if (!badge) { return; }
        badge.classList.add('pop');
        setTimeout(function () { badge.classList.remove('pop'); }, 600);
    }

    /* ---------- 13. Flash auto-dismiss (success alerts) ---------- */
    function initFlash() {
        doc.querySelectorAll('.alert-success').forEach(function (alert) {
            setTimeout(function () {
                alert.style.transition = 'opacity .5s, transform .5s';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-8px)';
                setTimeout(function () { alert.remove(); }, 550);
            }, 5000);
        });
    }

    /* ---------- 14. Hero parallax on mouse ---------- */
    function initHeroParallax() {
        var hero = doc.querySelector('.hero');
        if (!hero) { return; }
        hero.addEventListener('mousemove', function (e) {
            var r = hero.getBoundingClientRect();
            var x = (e.clientX - r.left) / r.width - 0.5;
            var y = (e.clientY - r.top) / r.height - 0.5;
            var shapes = hero.querySelectorAll('.hero-shapes span');
            shapes.forEach(function (shape, i) {
                var depth = (i % 4) + 1;
                shape.style.transition = 'transform .3s ease-out';
                shape.style.transform = 'translate(' + (x * depth * 14) + 'px, ' + (y * depth * 10) + 'px)';
            });
        });
        hero.addEventListener('mouseleave', function () {
            hero.querySelectorAll('.hero-shapes span').forEach(function (shape) {
                shape.style.transform = 'translate(0, 0)';
            });
        });
    }

    /* ---------- Init ---------- */
    function init() {
        initReveal();
        initStickyHeader();
        initToTop();
        initRipple();
        initSteppers();
        initAddToCart();
        initCounters();
        initCharts();
        initStarBars();
        initAdminSidebar();
        initActiveNav();
        initCartBadge();
        initFlash();
        initHeroParallax();
    }

    if (doc.readyState === 'loading') {
        doc.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();