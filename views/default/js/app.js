// =========================================
// Importaciones
// =========================================
import { Badge } from './plugins/Badge.js';
import { DropdownInit, addRemove, DropdownHandler } from './plugins/Dropdown.js';
import { Echo } from './plugins/Echo.js';
import { FetchSend } from './plugins/FetchSend.js';
import { lazyload } from './plugins/Lazy.js';
import { Modal } from './plugins/Modal.js';
import { SvgIcon } from './plugins/SvgIcon.js';

// =========================================
// Variables globales
// =========================================
const { notifications, messages, action } = AppData.system;

// =========================================
// Notificaciones
// =========================================
window.notificationes = {
	cache: {},
	async ajax(page, data = {}, callback = () => {}, type = 'text') {
		try {
			const response = await FetchSend(page, data, type);
			callback(response);
		} catch (error) {
			console.error('Error en FetchSend:', error);
			callback('<p class="text-red-600">Error al cargar datos</p>');
		}
	},
	last() {
		const trigger = document.querySelector('[name="Notificaciones"]');
		const panel = document.getElementById('ListNotificaciones');
		const total = parseInt(trigger?.dataset.popup || 0);
		if (!trigger || !panel) return;
		const isClosed = panel.classList.contains('dropoff');
		if (!isClosed) return this.close();
		this.show();
		if (typeof this.cache.last === 'undefined') {
			this.ajax('/notificaciones-ajax.php', { action: 'last' }, (response) => {
				this.cache.last = response;
				this.show();
			});
		}
	},
	show() {
		const panel = document.getElementById('ListNotificaciones');
		const content = panel?.querySelector('.list');
		if (!panel) return;
		panel.classList.replace('dropoff', 'dropon');
		if (this.cache.last && content) content.innerHTML = this.cache.last;
	},
	close() {
		const panel = document.getElementById('ListNotificaciones');
		if (panel) panel.classList.replace('dropon', 'dropoff');
	},
	popup(count) {
		const trigger = document.querySelector('[name="Notificaciones"]');
		if (trigger) Badge.init(trigger, count);
	}
};

// =========================================
// Usuario
// =========================================
window.usuario = {
	last() {
		const elem = document.getElementById('ListUsuario');
		if (!elem) return;
		elem.classList.contains('dropoff') ? this.show(elem) : this.close(elem);
	},
	show: (elem) => addRemove(elem, false),
	close: (elem) => addRemove(elem)
};

// =========================================
// Boxes Animados
// =========================================
const boxes = document.querySelectorAll('[data-box="item"]');

const boxesEffects = () => {
	const trigger = window.innerHeight * 0.9;
	boxes.forEach((box) => {
		const visible = box.getBoundingClientRect().top < trigger;
		box.classList.toggle('show', visible);
	});
};

window.addEventListener('scroll', boxesEffects);

// =========================================
// Menú responsive
// =========================================
const menuToggle = document.querySelector('.menu-toggle');
const menu = document.getElementById('main_menu');

menuToggle.addEventListener('click', () => {
	const isOpen = menuToggle.dataset.show === 'true';
	menu.classList.toggle('show', !isOpen);
	menuToggle.dataset.show = String(!isOpen);
});

// =========================================
// Inicializaciones
// =========================================
if(notifications > 0) notificationes.popup(notifications);
if(messages > 0 && action !== 'leer') mensaje.popup(messages);

SvgIcon();
DropdownInit();
lazyload.refresh();
boxesEffects();
DropdownHandler([
	{
		button: '[name="Usuario"]',
		dropdown: '#ListUsuario',
		handler: () => usuario.last()
	},
	{
		button: '[name="Mensajes"]',
		dropdown: '#ListMensajes',
		handler: () => mensaje.last()
	},
	{
		button: '[name="Notificaciones"]',
		dropdown: '#ListNotificaciones',
		handler: () => notificationes.last()
	}
]);