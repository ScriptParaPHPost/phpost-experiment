import { Input, ShowPassword } from './plugins/input.js';
import { passwordStrength } from './plugins/passwordStrength.js';

(() => {
	'use strict';
	//
	document.addEventListener("DOMContentLoaded", function(){
		passwordStrength(Input.get('password'));
		ShowPassword();
	});


})();