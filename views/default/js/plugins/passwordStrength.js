/**
 * Plugin: passwordStrength
 * Evalúa la fuerza de una contraseña y actualiza los elementos del DOM.
 * @author Miguel
 * @version 1.0.0
 */

export function passwordStrength(inputSelector, options = {}) {
	const input = typeof inputSelector === 'string'
		? document.querySelector(inputSelector)
		: inputSelector;

	if (!input) return;

	const defaultOptions = {
		rules: {
			symbol: /[^A-Za-z0-9]/,
			uppercase: /[A-Z]/,
			number: /\d/,
			length: /.{8,}/
		},
		statusElements: {
			symbol: '#symbol',
			uppercase: '#uppercase',
			number: '#number',
			length: '#length',
		},
		strengthIndicator: '#strength'
	};

	const config = { ...defaultOptions, ...options };
	const ruleKeys = Object.keys(config.rules);

	function updateStrength() {
		let score = 0;

		ruleKeys.forEach(rule => {
			const passed = config.rules[rule].test(input.value);
			const el = document.querySelector(config.statusElements[rule]);
			if (el) {
				el.classList.toggle('valid', passed);
				el.classList.toggle('invalid', !passed);
			}
			if (passed) score++;
		});

		const indicator = document.querySelector(config.strengthIndicator);
		if (indicator) {
			if (score === ruleKeys.length) {
				indicator.textContent = '✔ Fuerte';
				indicator.style.color = 'darkgreen';
			} else if (score >= 2) {
				indicator.textContent = '⚠ Media';
				indicator.style.color = 'orange';
			} else {
				indicator.textContent = '❌ Débil';
				indicator.style.color = 'darkred';
			}
		}
	}

	input.addEventListener('input', updateStrength);
}
