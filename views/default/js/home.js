import { Echo } from './plugins/Echo.js';
import { FetchSend } from './plugins/FetchSend.js';

/**
 * Actualiza los últimos comentarios
 */
const actualizar_comentarios = async () => {
	const comentarios = document.getElementById('ultimos_comentarios');
	if (!comentarios) return;

	comentarios.innerHTML = `<div class="py-4 text-center text-xl">Esperando...</div>`;
	try {
		const response = await FetchSend('/posts-last-comentarios.php', {}, 'text');
		setTimeout(() => {
			comentarios.innerHTML = response;
		}, 1500);
	} catch (error) {
		comentarios.innerHTML = `<div class="py-4 text-center text-red-600">Error al cargar los comentarios</div>`;
		console.error(error);
	}
};

const TopsTabs = async (parent, filter, action = 'posts') => {
	const box = document.getElementById(parent);
	const tabs = box?.querySelectorAll('.filterByTabs span');
	const BoxFilter = box?.querySelector('.box_cuerpo #filter');
	if (!box || !BoxFilter) return;

	// Marcar tab activo
	tabs.forEach(tab => tab.classList.toggle('font-bold', tab.id === filter));

	// Mostrar loader
	BoxFilter.innerHTML = `<div class="py-4 text-center text-xl">Esperando...</div>`;

	// Clave para el caché
	const cacheKey = `tops-${action}-${filter}`;
	const cacheData = localStorage.getItem(cacheKey);

	// Intentar usar caché válido
	if (cacheData) {
		const { data, timestamp } = JSON.parse(cacheData);
		const now = Date.now();
		if (now - timestamp < 3600 * 1000) { // Menos de 1 hora
			renderTops(BoxFilter, data, action);
			return;
		}
	}

	// Si no hay caché o expiró, obtener desde el servidor
	try {
		const response = await FetchSend(`/tops-${action}-filter.php`, { filter });
		if (Array.isArray(response)) {
			// Guardar en caché con timestamp
			localStorage.setItem(cacheKey, JSON.stringify({ data: response, timestamp: Date.now() }));
			renderTops(BoxFilter, response, action);
		} else {
			renderError(BoxFilter, `No hay ${action === 'posts' ? 'posts' : 'usuarios'}`);
		}
	} catch (error) {
		console.error(error);
		renderError(BoxFilter, 'Error al cargar el contenido');
	}
};

/**
 * Renderiza los resultados
 */
const renderTops = (container, data, action) => {
	if (!data.length) {
		renderError(container, `No hay ${action === 'posts' ? 'posts' : 'usuarios'}`);
		return;
	}
	const html = data.map(({ puntos, title, url }) => `
		<li class="flex justify-start items-center mb-1">
			<a class="flex-grow truncate" href="${url}">${title}</a>
			<span class="px-3">${puntos}</span>
		</li>
	`).join('');
	container.innerHTML = html;
};

/**
 * Muestra un mensaje de error
 */
const renderError = (container, message) => {
	container.innerHTML = `<div class="text-center text-red-600 py-3">${message}</div>`;
};


// Registrar funciones globales al cargar
document.addEventListener("DOMContentLoaded", () => {
	window.actualizar_comentarios = actualizar_comentarios;
	window.TopsTabs = TopsTabs;
});