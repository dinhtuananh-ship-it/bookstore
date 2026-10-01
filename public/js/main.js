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

    /* ---------- 15. Admin collapsible form panels (xổ ra kiểu thêm sách) ---------- */
    function initCollapsible() {
        doc.addEventListener('click', function (e) {
            var toggler = e.target.closest('[data-toggle-target]');
            if (!toggler) { return; }
            e.preventDefault();
            var target = doc.getElementById(toggler.getAttribute('data-toggle-target'));
            if (!target) { return; }
            var isOpen = target.classList.contains('open');
            // Đóng các panel khác cùng nhóm để giao diện gọn
            var group = toggler.getAttribute('data-toggle-group');
            if (group) {
                doc.querySelectorAll('[data-toggle-group="' + group + '"]').forEach(function (btn) {
                    var other = doc.getElementById(btn.getAttribute('data-toggle-target'));
                    if (other && other !== target) { other.classList.remove('open'); }
                    if (btn !== toggler) { btn.classList.remove('active'); }
                });
            }
            target.classList.toggle('open', !isOpen);
            toggler.classList.toggle('active', !isOpen);
            if (!isOpen) {
                setTimeout(function () {
                    target.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    var first = target.querySelector('input, select, textarea');
                    if (first) { first.focus({ preventScroll: true }); }
                }, 60);
            }
        });
        // Mở panel Sửa khi quay lại trang có lỗi (giữ id trên URL: #edit-3, #add-panel)
        if (window.location.hash) {
            var el = doc.getElementById(window.location.hash.substring(1));
            if (el && el.classList.contains('collapsible')) { el.classList.add('open'); }
        }
    }

    /* ---------- 16. Modal popup (dùng cho Thêm/Sửa khuyến mãi) ---------- */
    function openModal(overlay) {
        if (!overlay) { return; }
        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden', 'false');
        body.classList.add('modal-open');
        var first = overlay.querySelector('.modal-body input, .modal-body select, .modal-body textarea');
        if (first) { setTimeout(function () { first.focus({ preventScroll: true }); }, 120); }
    }
    function closeModal(overlay) {
        if (!overlay) { return; }
        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
        if (!doc.querySelector('.modal-overlay.open')) {
            body.classList.remove('modal-open');
        }
    }
    function initModals() {
        doc.addEventListener('click', function (e) {
            var opener = e.target.closest('[data-modal-open]');
            if (opener) {
                e.preventDefault();
                openModal(doc.getElementById(opener.getAttribute('data-modal-open')));
                return;
            }
            if (e.target.closest('[data-modal-close]')) {
                closeModal(e.target.closest('.modal-overlay'));
                return;
            }
            if (e.target.classList && e.target.classList.contains('modal-overlay')) {
                closeModal(e.target);
            }
        });
        doc.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                doc.querySelectorAll('.modal-overlay.open').forEach(closeModal);
            }
        });
    }

    /* ---------- 17. Nạp đúng dữ liệu mã khuyến mãi vào modal Sửa ---------- */
    function initCouponEditModal() {
        var modal = doc.getElementById('coupon-edit-modal');
        if (!modal) { return; }
        var form = modal.querySelector('form');
        var titleCode = modal.querySelector('[data-edit-title-code]');
        doc.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-edit-coupon]');
            if (!btn) { return; }
            e.preventDefault();
            var get = function (name) { return btn.getAttribute('data-' + name) || ''; };
            form.querySelector('[name="id"]').value = get('id');
            form.querySelector('[name="code"]').value = get('code');
            form.querySelector('[name="type"]').value = get('type') || 'percent';
            form.querySelector('[name="value"]').value = get('value');
            form.querySelector('[name="min_order"]').value = get('min-order');
            form.querySelector('[name="max_uses"]').value = get('max-uses');
            form.querySelector('[name="start_date"]').value = get('start');
            form.querySelector('[name="end_date"]').value = get('end');
            form.querySelector('[name="status"]').value = get('status') || '1';
            if (titleCode) { titleCode.textContent = get('code'); }
            openModal(modal);
        });
    }

    /* ---------- 18. Chống lăn chuột làm nhảy số trong ô number (giá, tồn kho...) ---------- */
    function initNumberWheelGuard() {
        // Khi đang nhập số mà lăn chuột thì trình duyệt tự tăng giảm giá trị -> blur để giữ nguyên số đang nhập
        doc.addEventListener('wheel', function () {
            var el = doc.activeElement;
            if (el && el.tagName === 'INPUT' && el.type === 'number') {
                el.blur();
            }
        }, { passive: true });
        doc.querySelectorAll('input[type="number"]').forEach(function (input) {
            input.addEventListener('wheel', function (e) {
                e.preventDefault();
                input.blur();
            }, { passive: false });
            // Chỉ cho đổi số bằng phím lên xuống khi đã focus, không cho lăn chuột đổi ngầm
            input.addEventListener('focus', function () {
                input.setAttribute('data-wheel-guard', '1');
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
        initCollapsible();
        initModals();
        initCouponEditModal();
        initNumberWheelGuard();
    }

    if (doc.readyState === 'loading') {
        doc.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();