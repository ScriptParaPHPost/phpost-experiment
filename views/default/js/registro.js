import { Echo } from './plugins/Echo.js';
import { FetchSend } from './plugins/FetchSend.js';
import { Input, ShowPassword, helper } from './plugins/input.js';
import { Modal } from './plugins/Modal.js';
import { passwordStrength } from './plugins/passwordStrength.js';

(() => {
	'use strict';

	// Comprobamos con patrones
	const regex = {
		nickname: /^[a-zA-Z0-9\_\-]{4,20}$/,
		password: /^.{4,32}$/,
		email: /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/,
	}
	// Verificamos
	let status = {
		nickname: false,
		password: false,
		email: false,
		sexo: true, // Ya que es opcional.
		terminos: false
	}
	// Función genérica para validar y chequear en servidor
	async function validateField(name, regexRule, endpoint, extraCheck = null) {
		const value = Input.getValue(name);
		if (!regexRule.test(value)) {
			helper.message(name, `El campo ${name} es inválido.`, 0);
			status[name] = false;
			return;
		}
		if (extraCheck && !extraCheck(value)) {
			return;
		}
		const res = await FetchSend(endpoint, { [name]: value });
		helper.message(name, res.message, res.success ? 1 : 0);
		status[name] = res.success;
	}
	// Utilidad para obtener todos los valores del formulario automáticamente
	function getFormValues(formElement) {
		const formData = new FormData(formElement);
		const values = {};
		for (const [key, value] of formData.entries()) {
			if (values[key] !== undefined) {
				if (!Array.isArray(values[key])) values[key] = [values[key]];
				values[key].push(value);
			} else {
				values[key] = value;
			}
		}
		return values;
	}

	Input.get('nickname').addEventListener('keyup', () => validateField('nickname', regex.nickname, '/registro-check-nick.php'));
	Input.get('email').addEventListener('keyup', () => validateField('email', regex.email, '/registro-check-email.php'));

	Input.get('password').addEventListener('keyup', () => {
		const value = Input.getValue('password');
		const nick = Input.getValue('nickname');
		if (!regex.password.test(value)) {
			helper.message('password', 'Contraseña no válida.', 0);
			status.password = false;
			return;
		}
		if (value === nick) {
			helper.message('password', 'No puede ser igual al nick.', 0);
			status.password = false;
			return;
		}
		helper.message('password', 'Contraseña aceptable.', 1);
		status.password = true;
	});

	Input.all('sexo').forEach(sexo => sexo.addEventListener('click', e => status.sexo = ['0', '1', '2', '3'].includes(e.target.value)));

	Input.get('terminos').addEventListener('click', e => {
		let isChecked = e.target.checked;
		helper.message('terminos', 'Terminos aceptados', isChecked ? 1 : 0);
		status.terminos = isChecked;
	});

	//
	document.addEventListener("DOMContentLoaded", function(){
		passwordStrength(Input.get('password'));
		ShowPassword();
		grecaptcha.ready(() => grecaptcha.execute(siteKey, { action: 'submit' }).then(token => {
			const response = document.getElementById('response');
			if (response) {
				response.value = token;
			} else {
				console.warn('Elemento #response no encontrado en el DOM.');
			}
		}));
	});

	document.querySelector('form[name=formulario]').addEventListener('submit', async e => {
		e.preventDefault();
		// Verificar que los status sean todos TRUE
		const allValid = Object.values(status).every(Boolean);
		if (!allValid) {
			Echo.danger('Debes completar correctamente todos los campos.');
			return;
		}

		Modal.open({
			title: 'Espere por favor...',
			loading: true,
			loadingText: 'En este momento se esta creando su cuenta!',
			showFooter: false
		});

		const data = getFormValues(e.target);
		const response = await FetchSend('/registro-nuevo.php', data);
		
		if(response.message) {
			Modal.open({
				title: 'Finalizado',
				content: response.message,
				buttonContinueText: 'Ir a mi cuenta',
				onContinue: () => {
					window.location.href = AppData.url + '/cuenta/';
				}
			});
		} else Echo.danger(response.message);
	});

})();