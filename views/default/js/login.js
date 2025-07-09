import { Echo } from './plugins/Echo.js';
import { FetchSend } from './plugins/FetchSend.js';
import { Input, ShowPassword } from './plugins/input.js';
import { Modal } from './plugins/Modal.js';

(() => {
	'use strict';

	const { url: apiUrl } = AppData;
	const button = Input.get('iniciar');
	const spanButton = document.querySelectorAll('span[role=button]');

	button.addEventListener('click', async event => {
		event.preventDefault();

		if(Input.isEmpty('nickname') || Input.isEmpty('password')) {
			Input.focus('nickname');
			Echo.danger('Todos los campos deben ser completados...');
			return;
		}
		
		// Mostrar loading (opcional)
		Modal.open({
			title: 'Espere por favor...',
			loading: true,
			loadingText: 'Iniciando sesión...',
			showFooter: false
		});

		try {
			const response = await FetchSend('/login-user.php', {
				nickname: Input.getValue('nickname'),
				password: Input.getValue('password'),
				remember: Input.isChecked('remember') ? true : false
			});
			
			window.location.href = response.redirect;
	
			if (!response.status) {
				Modal.open({
					title: 'Hubo un problema',
					content: response.message,
					onContinue: () => {
						window.location.href = location.href;
					}
				});
			}
		} catch (e) {
			console.error(e);
			Echo.danger('Ocurrió un error inesperado.');
		}
	});

	function actionExtras(type, next = false) {
		let title = (type === 'account') ? 'Reenviar validación' : 'Recuperar Contraseña';
		if(!next) {
			Modal.open({
				title, 
				content: `<label class="block relative">
					<input type="text" autocomplete="off" name="email" placeholder="jhondoe@example.com" class="bg-blue-100 bg-opacity:30 border-blue-300 p-3 block w-full rounded">
				</label>`,
				onContinue: () => actionExtras(type, true),
				onCancel: () => Modal.close()
			});
			return;
		}
					
		Modal.ajax(`/verificar-${type}.php`, {
			title,
			postData: {
				email: document.querySelector('input[name=email]').value
			},
			loadingText: 'Enviando...',
			buttonContinueText: 'Aceptar',
			onContinue: () => Modal.close(),
			showCancelButton: false
		});
	}

	spanButton.forEach( span => {
		span.addEventListener('click', async event => {
			actionExtras(span.dataset.action)
		});
	});

	document.addEventListener("DOMContentLoaded", () => ShowPassword() )

})();