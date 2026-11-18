document.addEventListener('DOMContentLoaded', function () {
	// ---- Theme toggle (light/dark) — applies to the whole site via <body> class ----
	var toggleBtn = document.querySelector('.qh-theme-toggle');

	var saved = window.localStorage.getItem('qh-theme');
	if (saved === 'dark') {
		document.body.classList.add('qh-dark');
	}

	if (toggleBtn) {
		// Keep the accessible state in sync with the visual state.
		var syncToggle = function (isDark) {
			toggleBtn.innerHTML = '<span aria-hidden="true">' + (isDark ? '\u2600\uFE0F' : '\uD83C\uDF19') + '</span>';
			toggleBtn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
			toggleBtn.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
		};

		syncToggle(document.body.classList.contains('qh-dark'));

		toggleBtn.addEventListener('click', function () {
			var isDark = document.body.classList.toggle('qh-dark');
			syncToggle(isDark);
			window.localStorage.setItem('qh-theme', isDark ? 'dark' : 'light');
		});
	}

	// ---- Breadcrumb "Back" button ----
	// Moved out of an inline onclick attribute on an <a href="#">; the
	// behaviour is identical, but it is now a real button and no longer a
	// crawlable link that points at the current page.
	var backButtons = document.querySelectorAll('[data-qh-back]');
	backButtons.forEach(function (btn) {
		btn.addEventListener('click', function () {
			if (window.history.length > 1) {
				window.history.back();
			} else {
				window.location.href = '/';
			}
		});
	});

	// ---- Nav dropdowns (All Tools / About) ----
	var dropdowns = document.querySelectorAll('.qh-dropdown');

	// Closes every dropdown and keeps each toggle's aria-expanded accurate.
	var closeAllDropdowns = function () {
		dropdowns.forEach(function (d) {
			d.classList.remove('qh-dropdown-open');
			var t = d.querySelector('.qh-dropdown-toggle');
			if (t) {
				t.setAttribute('aria-expanded', 'false');
			}
		});
	};

	dropdowns.forEach(function (dropdown) {
		var toggle = dropdown.querySelector('.qh-dropdown-toggle');
		if (!toggle) {
			return;
		}
		toggle.addEventListener('click', function (e) {
			e.stopPropagation();
			var isOpen = dropdown.classList.contains('qh-dropdown-open');
			closeAllDropdowns();
			if (!isOpen) {
				dropdown.classList.add('qh-dropdown-open');
				toggle.setAttribute('aria-expanded', 'true');
			}
		});

		// Escape closes the menu and returns focus to its button.
		dropdown.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' || e.key === 'Esc') {
				closeAllDropdowns();
				toggle.focus();
			}
		});
	});

	document.addEventListener('click', function () {
		closeAllDropdowns();
	});

	// ---- Live search (homepage tool grid) ----
	var searchInput = document.querySelector('.qh-search');
	if (!searchInput) {
		return;
	}

	searchInput.addEventListener('input', function () {
		var query = searchInput.value.trim().toLowerCase();
		var cards = document.querySelectorAll('.qh-wrap .qh-card');
		var categories = document.querySelectorAll('.qh-wrap .qh-category');

		cards.forEach(function (card) {
			var name = card.getAttribute('data-name') || '';
			if (query === '' || name.indexOf(query) !== -1) {
				card.classList.remove('qh-hidden');
			} else {
				card.classList.add('qh-hidden');
			}
		});

		categories.forEach(function (cat) {
			var visibleCards = cat.querySelectorAll('.qh-card:not(.qh-hidden)');
			cat.style.display = visibleCards.length > 0 ? '' : 'none';
		});
	});
});
