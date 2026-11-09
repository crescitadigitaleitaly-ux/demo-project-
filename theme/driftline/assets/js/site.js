/* Driftline front-end behaviour: menu, reveals, forms. No dependencies. */
(function () {
	'use strict';
	var d = document;
	window.dlReady = true;

	/* Header shadow on scroll */
	var header = d.querySelector('.dl-header');
	if (header) {
		var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 8); };
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	/* Mobile menu */
	var burger = d.querySelector('.dl-burger');
	var menu = d.getElementById('dl-menu');
	if (burger && menu) {
		menu.querySelectorAll('li').forEach(function (li, i) { li.style.setProperty('--i', i); });
		var setOpen = function (open) {
			burger.setAttribute('aria-expanded', open ? 'true' : 'false');
			burger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
			menu.classList.toggle('is-open', open);
			if (open) { menu.removeAttribute('inert'); } else { menu.setAttribute('inert', ''); }
			d.body.classList.toggle('dl-menu-open', open);
		};
		setOpen(false);
		burger.addEventListener('click', function () { setOpen(burger.getAttribute('aria-expanded') !== 'true'); });
		d.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && burger.getAttribute('aria-expanded') === 'true') { setOpen(false); burger.focus(); }
		});
		menu.addEventListener('click', function (e) { if (e.target.closest('a')) { setOpen(false); } });
		window.matchMedia('(min-width: 861px)').addEventListener('change', function (e) { if (e.matches) { setOpen(false); } });
	}

	/* Reveal on scroll */
	var covers = d.querySelectorAll('.wp-block-cover.dl-panel, .wp-block-cover.dl-hero');
	var groups = [
		'.dl-section > *:not(.dl-cards):not(.dl-gallery):not(.wp-block-columns)',
		'.dl-section > .wp-block-columns > .wp-block-column',
		'.dl-cards > .dl-card',
		'.dl-gallery > figure',
		'.dl-cta > *'
	];
	var items = d.querySelectorAll(groups.join(','));
	items.forEach(function (el) {
		el.classList.add('dl-reveal');
		var sibs = Array.prototype.filter.call(el.parentNode.children, function (c) { return c.classList.contains('dl-reveal') || c === el; });
		el.style.setProperty('--d', Math.min(sibs.indexOf(el), 5));
	});
	if ('IntersectionObserver' in window) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
		items.forEach(function (el) { io.observe(el); });
		covers.forEach(function (el) { io.observe(el); });
	} else {
		items.forEach(function (el) { el.classList.add('is-in'); });
		covers.forEach(function (el) { el.classList.add('is-in'); });
	}

	/* Forms: prefill from the URL. Submission is a front-end demo only. */
	var params = new URLSearchParams(window.location.search);
	d.querySelectorAll('form.dl-form').forEach(function (form) {
		var status = form.querySelector('.dl-form__status');
		var service = params.get('service');
		var product = params.get('product');
		var sel = form.querySelector('select[name="service"]');
		if (sel && service) {
			Array.prototype.forEach.call(sel.options, function (o) { if (o.value === service) { sel.value = service; } });
		}
		if (product) {
			var hidden = form.querySelector('input[name="product"]');
			var picked = form.querySelector('.dl-form__picked');
			if (hidden && picked) {
				hidden.value = product.slice(0, 120);
				picked.textContent = 'You are enquiring about: ' + hidden.value;
				picked.hidden = false;
			}
		}
		form.addEventListener('submit', function (e) {
			// Front-end demo: validate, confirm, send nothing.
			e.preventDefault();
			if (!form.reportValidity()) { return; }
			status.textContent = form.getAttribute('data-success');
			status.hidden = false;
			form.reset();
			status.focus();
		});
	});

	/* Product gallery: thumbnails swap the main image */
	d.querySelectorAll('.dl-pgallery').forEach(function (g) {
		var main = g.querySelector('.dl-pgallery__main img');
		var thumbs = g.querySelectorAll('.dl-pgallery__thumbs button');
		thumbs.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var img = btn.querySelector('img');
				if (!main || !img) { return; }
				main.style.opacity = '0';
				setTimeout(function () {
					main.src = btn.getAttribute('data-full') || img.src;
					main.removeAttribute('srcset');
					main.alt = img.alt;
					main.style.opacity = '1';
				}, 180);
				thumbs.forEach(function (b) { b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'); });
			});
		});
	});
})();
