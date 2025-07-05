let mainMenuDropdown = null;

export function addRemove(el, add = true) {
	el.classList[(add ? 'remove' : 'add')]('dropon');
	el.classList[(add ? 'add' : 'remove')]('dropoff');
}

function targets(except = null) {
	// Oculta todos excepto el actual (si se pasa)
	document.querySelectorAll('[data-target]').forEach(el => {
		if (el !== except) addRemove(el);
	});
}

function dropdown() {
	mainMenuDropdown = document.getElementById('main_menu');
	const navLinks = mainMenuDropdown.querySelectorAll('nav a[name]');

	navLinks.forEach(link => {
		link.addEventListener('click', e => {
			e.preventDefault();

			const name = link.getAttribute('name');
			const target = document.querySelector(`[data-target="${name}"]`);

			if (!target) return;

			const isOpen = target.classList.contains('dropon');

			// Cierra todos menos este
			targets(isOpen ? null : target);

			// Alternar estado
			addRemove(target, isOpen); // si ya está abierto → cerralo
		});
	});
}

export function DropdownInit() {
	dropdown();

	document.addEventListener('click', e => {
		if (!mainMenuDropdown.contains(e.target)) {
			targets();
		}
	});
}

export function DropdownHandler(configs) {
	document.addEventListener('DOMContentLoaded', () => {
		document.body.addEventListener('click', (e) => {
			configs.forEach(({ button, dropdown, handler }) => {
				const btn = document.querySelector(button);
				const panel = document.querySelector(dropdown);
				if (!btn || !panel) return;
				const clickedOutside = !e.target.closest(dropdown) && !e.target.closest(button);
				const isVisible = panel.offsetParent !== null;
				if (isVisible && clickedOutside) {
					if (typeof handler === 'function') handler();
				}
			});
		});
	});
}
