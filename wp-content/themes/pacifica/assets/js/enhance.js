/**
 * Pacífica — progressive enhancement.
 *
 * Dependency-free. Two behaviours:
 *   1. Scroll reveal — adds `.is-inview` to `.pf-reveal` elements as they enter
 *      the viewport (IntersectionObserver), then stops observing.
 *   2. Header scrolled-state — toggles `.is-scrolled` on `.pf-header` past a
 *      small scroll threshold, for the sticky/transparent header transition.
 *
 * Both respect `prefers-reduced-motion`. No external dependencies.
 *
 * @package Pacifica
 */
(function () {
	'use strict';

	var prefersReducedMotion =
		window.matchMedia &&
		window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* ----------------------------------------------------------------------
	 * 1. Scroll reveal
	 * ------------------------------------------------------------------- */
	var revealEls = Array.prototype.slice.call(
		document.querySelectorAll('.pf-reveal, .pf-reveal-group, [data-pf-reveal]')
	);

	if (revealEls.length) {
		if (prefersReducedMotion || !('IntersectionObserver' in window)) {
			// No animation path: show everything immediately.
			revealEls.forEach(function (el) {
				el.classList.add('is-inview');
			});
		} else {
			var observer = new IntersectionObserver(
				function (entries, obs) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting) {
							entry.target.classList.add('is-inview');
							obs.unobserve(entry.target);
						}
					});
				},
				{ rootMargin: '0px 0px -10% 0px', threshold: 0.12 }
			);

			revealEls.forEach(function (el) {
				observer.observe(el);
			});
		}
	}

	/* ----------------------------------------------------------------------
	 * 2. Header scrolled state
	 * ------------------------------------------------------------------- */
	var header = document.querySelector('.pf-header');

	if (header) {
		var THRESHOLD = 8;
		var ticking = false;

		var applyState = function () {
			header.classList.toggle('is-scrolled', window.scrollY > THRESHOLD);
			ticking = false;
		};

		// Set the correct state on load (e.g. when restored mid-page).
		applyState();

		window.addEventListener(
			'scroll',
			function () {
				if (!ticking) {
					window.requestAnimationFrame(applyState);
					ticking = true;
				}
			},
			{ passive: true }
		);
	}
})();
