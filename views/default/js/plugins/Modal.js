const { url: baseApi, theme: basePath } = AppData;

let modalElement = null;
let options = {};

// Utilidad para crear un botón dinámico
function createButton({ id, text, primary }) {
	const btn = document.createElement('button');
	btn.id = id;
	btn.textContent = text;
	btn.className = `py-2 px-4 rounded ${primary
		? 'bg-neutral-800 hover:bg-neutral-900 text-neutral-50'
		: 'bg-neutral-50 hover:bg-neutral-100 text-black'
	}`;
	return btn;
}

// Crea la estructura del modal y sus elementos
function createModalStructure() {
	modalElement = document.createElement('div');
	modalElement.className = 'fixed top-0 left-0 w-full h-full flex justify-center items-center z-9999 blur-md bg-neutral-100 bg-opacity:20';
	modalElement.innerHTML = `
		<div class="modal w-1/3 bg-neutral-50 dark:bg-neutral-950 dark:text-neutral-100 rounded shadow">
			<div class="flex justify-between items-center p-3">
				<div class="font-bold text-lg" id="modalTitle"></div>
				<span class="block text-md cursor-pointer" id="modalClose">&times;</span>
			</div>
			<div class="py-3 px-6 min-h-auto max-h-50dvh overflow-y-auto">
				<div class="text-center py-6 hidden" id="modalLoading">
					<img src="${basePath}/images/loading_bar.gif" alt="Cargando...">
					<span class="font-medium text-lg uppercase block" id="loadingText">Cargando...</span>
				</div>
				<div id="modalContent"></div>
			</div>
			<div class="flex justify-center items-center gap-4 py-4 hidden" id="modalFooter"></div>
		</div>
	`;
	document.body.appendChild(modalElement);
	setupModalEvents();
}

// Configura eventos del modal (cerrar, teclas, backdrop)
function setupModalEvents() {
	modalElement.addEventListener('click', e => {
		if (e.target === modalElement && options.closeOnBackdrop) close();
	});
	modalElement.querySelector('#modalClose').addEventListener('click', close);
	document.addEventListener('keydown', handleKeydown);
}

// Detecta tecla ESC para cerrar
function handleKeydown(e) {
	if (e.key === 'Escape' && modalElement && options.closeOnBackdrop) close();
}

function handleButtons(options, modalElement, footer) {
	if (options.showFooter) {
		const footer = modalElement.querySelector('#modalFooter');
		footer.classList.remove('hidden');
		footer.innerHTML = '';

		if (options.showContinueButton) {
			const btnContinue = createButton({
				id: 'buttonContinue',
				text: options.buttonContinueText,
				primary: true
			});
			btnContinue.addEventListener('click', () => {
				if (typeof options.onContinue === 'function') options.onContinue();
			});
			footer.appendChild(btnContinue);
		}

		if (options.showCancelButton) {
			const btnCancel = createButton({
				id: 'buttonCancel',
				text: options.buttonCancelText,
				primary: false
			});
			btnCancel.addEventListener('click', () => {
				if (typeof options.onCancel === 'function') options.onCancel();
				close();
			});
			footer.appendChild(btnCancel);
		}
	} else {
		modalElement.querySelector('#modalFooter').classList.add('hidden');
	}
}

// Abre el modal con la configuración dada
function open(config = {}) {
	close();
	if (!modalElement) createModalStructure();

	options = {
		title: '',
		content: '',
		loading: false,
		showFooter: true,
		closeOnBackdrop: true,
		onContinue: null,
		onCancel: null,
		loadingText: 'Cargando...',
		buttonContinueText: 'Continuar',
		buttonCancelText: 'Cancelar',
		showContinueButton: true,
		showCancelButton: true,
		...config
	};

	if(options.loadingText !== 'Cargando...') {
		modalElement.querySelector('#loadingText').textContent = options.loadingText;
	}

	// Setea título y contenido
	modalElement.querySelector('#modalTitle').textContent = options.title;
	modalElement.querySelector('#modalContent').innerHTML = options.content || '';
	modalElement.querySelector('#modalLoading').classList.toggle('hidden', !options.loading);

	// Botones
	const footer = modalElement.querySelector('#modalFooter');
	footer.classList.toggle('hidden', !options.showFooter);
	footer.innerHTML = ''; // Limpia botones previos

	handleButtons(options, modalElement, footer);
	modalElement.classList.remove('hidden');
}

// Abre el modal cargando contenido vía AJAX
async function ajax(url, config = {}) {
	const { postData = null } = config;

	open({
		...config,
		content: '',
		loading: true
	});

	try {
		const fetchOptions = postData
			? {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: new URLSearchParams(postData).toString()
			}
			: {};

		const response = await fetch(baseApi + url, fetchOptions);
		const html = await response.text();
	
		if (!modalElement) return;
		modalElement.querySelector('#modalContent').innerHTML = html;
		modalElement.querySelector('#modalLoading').classList.add('hidden');
	} catch (err) {
		if (!modalElement) return;
		modalElement.querySelector('#modalContent').innerHTML =
			'<p class="text-red-600">Error al cargar el contenido</p>';
		modalElement.querySelector('#modalLoading').classList.add('hidden');
	}
}

// Cierra el modal y limpia
function close() {
	if (modalElement) {
		modalElement.remove();
		document.removeEventListener('keydown', handleKeydown);
	}
	modalElement = null;
	options = {};
}

// 📦 Exporta como plugin
export const Modal = {
	open,
	close,
	ajax
};
