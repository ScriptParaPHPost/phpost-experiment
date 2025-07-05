"use strict";

/**
 * @version 1.0.0
 * @author Miguel92
 */

// Opciones por defecto
const defaultOptions = { color: 'info', duration: 8 };

// Crear contenedor si no existe
function createContainer() {
	let container = document.querySelector('.__container');
	if (!container) {
		container = document.createElement('div');
		container.className = '__container z-999 flex flex-col gap-3 top-4 right-4 fixed';
		document.body.appendChild(container);
	}
	return container;
}

// Función base para mostrar el toast
function showEcho(message, options = {}) {
	Echo.clear();
	const settings = { ...defaultOptions, ...options };
	const { color, duration } = settings;

	const container = createContainer();

	const toast = document.createElement('div');
	toast.setAttribute('data-status', color);
	toast.className = `echo-toast show px-6 py-3 rounded`;
	toast.innerHTML = message;

	container.appendChild(toast);

	setTimeout(() => {
		toast.classList.remove('show');
		toast.addEventListener('transitionend', () => toast.remove());
	}, duration * 1000);
}

// Creamos Echo como función y le agregamos métodos
const Echo = (message, options = {}) => showEcho(message, options);

Echo.success = (msg, options = {}) => showEcho(msg, { color: 'success', ...options });
Echo.danger  = (msg, options = {}) => showEcho(msg, { color: 'danger', ...options });
Echo.info    = (msg, options = {}) => showEcho(msg, { color: 'info', ...options });
Echo.warning = (msg, options = {}) => showEcho(msg, { color: 'warning', ...options });

/**
 * Elimina todos los mensajes Echo visibles inmediatamente.
 */
Echo.clear = () => {
	const container = document.querySelector('.__container');
	if (container) {
		const allToasts = container.querySelectorAll('.echo-toast');
		allToasts.forEach(toast => toast.remove());
	}
};

// Exportamos después de haber definido todo
export { Echo };