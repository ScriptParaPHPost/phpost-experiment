const password = document.getElementById('password');
const strength = document.getElementById('strength');

const rules = {
	symbol: /[^A-Za-z0-9]/,
	uppercase: /[A-Z]/,
	number: /\d/,
	length: /.{8,}/,
};

const statusElements = Object.fromEntries(Object.keys(rules).map(key => [key, document.getElementById(key)]));

function verificarPassword() {
	const pass = password.value;
	let score = 0;
	//
	for (const [rule, regex] of Object.entries(rules)) {
	  const passed = regex.test(pass);
	  score += passed ? 1 : 0;
	  statusElements[rule].classList.toggle('text-green-600', passed);
	  statusElements[rule].classList.toggle('text-red-600', !passed);
	}
	let msg = '', color = '', valid = false;
	if (score === 4) {
	  msg = '✔ Contraseña fuerte';
	  color = 'green';
	  valid = true;
	} else if (score >= 2) {
	  msg = '⚠ Contraseña media';
	  color = 'orange';
	} else {
	  msg = '❌ Contraseña débil';
	  color = 'red';
	}
	strength.textContent = msg;
	strength.style.color = color;
	password.classList.toggle('ok', valid);
	password.classList.toggle('fail', !valid);
}
password.addEventListener('input', verificarPassword);
window.addEventListener('DOMContentLoaded', () => {
  	if (password.value.trim()) verificarPassword();
});