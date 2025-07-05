/**
 * Input — utilidades para manipular y validar inputs (checkbox, radio, texto, select, textarea).
 * @author Miguel
 */
export const Input = {
	/**
	 * Contexto de búsqueda (por defecto, `document`).
	 * Podés cambiarlo a un formulario específico si querés limitar el alcance.
	 */
	context: document,

	/**
	 * Obtiene el primer input/select/textarea con un determinado name.
	 * @param {string} name - Nombre del input.
	 * @returns {HTMLElement|null}
	 */
	get(name = '') {
		return this.context.querySelector(`[name="${name}"]`);
	},

	/**
	 * Obtiene todos los inputs/selects/checkboxes con ese name (útil para radios o checkboxes múltiples).
	 * @param {string} name - Nombre del grupo.
	 * @returns {HTMLElement[]}
	 */
	all(name = '') {
		return Array.from(this.context.querySelectorAll(`[name="${name}"]`));
	},

	/**
	 * Verifica si un campo está vacío.
	 * Soporta input, textarea, checkbox y radio.
	 * @param {string} name - Nombre del campo.
	 * @returns {boolean}
	 */
	isEmpty(name = '') {
		const el = this.get(name);
		if (!el) return true;

		const type = el.type;
		if (type === 'checkbox' || type === 'radio') return !el.checked;

		return el.value?.trim() === '';
	},

	/**
	 * Verifica si un checkbox o radio está tildado.
	 * @param {string} name - Nombre del input.
	 * @returns {boolean}
	 */
	isChecked(name = '') {
		const el = this.get(name);
		return el?.type === 'checkbox' || el?.type === 'radio' ? el.checked : false;
	},

	/**
	 * Devuelve el valor actual de un campo.
	 * Checkbox → `true/false`
	 * Radio → `value` del seleccionado
	 * Otros → texto
	 * @param {string} name - Nombre del campo.
	 * @returns {string|boolean|null}
	 */
	getValue(name = '') {
		const el = this.get(name);
		if (!el) return null;
		const type = el.type;
		if (type === 'checkbox') return el.checked;
		if (type === 'radio') {
			const selected = this.all(name).find(e => e.checked);
			return selected?.value ?? null;
		}
		return el.value;
	},

	/**
	 * Verifica si un campo contiene un email válido.
	 * @param {string} name - Nombre del campo.
	 * @returns {boolean}
	 */
	isValidEmail(name = '') {
		const val = this.getValue(name);
		return typeof val === 'string' && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
	},

	/**
	 * Verifica si el valor del campo es numérico (permite decimales).
	 * @param {string} name - Nombre del campo.
	 * @returns {boolean}
	 */
	isNumeric(name = '') {
		const val = this.getValue(name);
		return val !== null && !isNaN(val);
	},

	/**
	 * Verifica si la longitud del valor está entre un mínimo y un máximo.
	 * Solo aplica para campos de texto.
	 * @param {string} name - Nombre del campo.
	 * @param {number} min - Longitud mínima.
	 * @param {number} max - Longitud máxima.
	 * @returns {boolean}
	 */
	isLengthBetween(name = '', min = 0, max = Infinity) {
		const val = this.getValue(name);
		if (typeof val !== 'string') return false;
		const len = val.trim().length;
		return len >= min && len <= max;
	},

	/**
	 * Enfoca el campo.
	 * @param {string} name - Nombre del campo.
	 */
	focus(name = '') {
		const el = this.get(name);
		if (el) el.focus();
	},

	/**
	 * Limpia el valor del input (texto, radio, checkbox, etc).
	 * @param {string} name - Nombre del campo.
	 */
	clear(name = '') {
		const el = this.get(name);
		if (!el) return;

		const type = el.type;

		if (type === 'checkbox' || type === 'radio') {
			this.all(name).forEach(el => el.checked = false);
		} else {
			el.value = '';
		}
	},

	/**
	 * Habilita o deshabilita un input.
	 * @param {string} name - Nombre del campo.
	 * @param {boolean} state - `true` para deshabilitar, `false` para habilitar.
	 */
	disable(name = '', state = true) {
		const el = this.get(name);
		if (el) el.disabled = state;
	}
};

const _btnShow = document.querySelector("[data-show='password']");
const _input = document.querySelector("input[type='password']");

export function ShowPassword() {
	if (!_btnShow || !_input) return;

	_btnShow.addEventListener('click', () => {
		const isPassword = _input.getAttribute('type') === 'password';
		_input.setAttribute('type', isPassword ? 'text' : 'password');
		_btnShow.innerText = isPassword ? 'Hide' : 'Show';
	});
}

export const helper = {
	context: document,
	// Selecciona el elemento helper por nombre
	selector(name) {
		return this.context.querySelector(`.helper-${name}`);
	},
	remclass(el) {
		el.classList.remove('helper-success', 'helper-danger', 'helper-loading');
	},
	// Muestra el mensaje con estado (0 = danger | 1 = success | 2 = loading)
	message(name, text = '', status = 1) {
		const el = this.selector(name);
		if (!el) return;
		// Limpiar clases anteriores
		this.remclass(el);
		// Añadir la clase según el estado
		el.classList.add(status === 1 ? 'helper-success' : (status === 2 ? 'helper-loading' : 'helper-danger'));
		// Mostrar mensaje
		el.textContent = text;
	},
	// Limpia el mensaje y clases
	clear(name) {
		const el = this.selector(name);
		if (!el) return;
		el.innerText = '';
		this.remclass(el);
	}
};
/*

if (Input.isEmpty('username')) {
	alert('El campo usuario está vacío');
	Input.focus('username');
}
if (Input.isChecked('remember')) {
	console.log('Recordar sesión activado');
} else {
	console.log('Recordar sesión desactivado');
}

const email = Input.getValue('email');
console.log('Email ingresado:', email);
const gender = Input.getValue('gender'); // para grupo radio buttons
console.log('Género seleccionado:', gender);

if (!Input.isValidEmail('email')) {
	alert('Por favor ingresa un email válido');
}

if (!Input.isLengthBetween('password', 6, 12)) {
	alert('La contraseña debe tener entre 6 y 12 caracteres');
}

Input.clear('comentarios');
Input.clear('gender'); // desmarca radios si aplica
Input.clear('subscribe'); // desmarca checkbox

Input.disable('submit', true);  // deshabilita botón enviar
Input.disable('submit', false); // habilita botón enviar

Input.focus('username');

if (!Input.isNumeric('age')) {
	alert('La edad debe ser un número válido');
}

*/