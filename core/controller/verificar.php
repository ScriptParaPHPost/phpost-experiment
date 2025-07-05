<?php

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

require_once __DIR__ . '/../../header.php';

$PB = new PageBootstrap('verificar', 1, $container->get('tsCore'), $container->get('tsSmarty'));

$type = (int)filter_input(INPUT_GET, 'type', FILTER_SANITIZE_NUMBER_INT) ?? 0;
$PB->title = ($type === 1 ? "Recuperar contrase&ntilde;a " : "Validar cuenta ") . $tsCore->settings['titulo'];

$tsVerificar = $container->loader(
	'class/c.verificar.php', 'tsVerificar', 
	fn($c) => new tsVerificar($c->get('tsCore'), $c->get('Junk'), $c->get('PasswordManager'))
);

$action = ($type === 1 ? 'password' : 'validar');
$email = $tsCore->setSecure(filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL));
$code = $tsCore->setSecure(filter_input(INPUT_GET, 'hash', FILTER_UNSAFE_RAW)); # key

$tsData = $tsVerificar->verifyEmail($email, $action);

if($tsVerificar->delContactsOld($tsData) || !is_array($tsData)) {
	$PB->page = 'aviso';
	$PB->ajax = 0;
	$smarty->assign("tsAviso", [
		'titulo' => 'Lo sientimos mucho', 
		'mensaje' => $tsData ?: 'No existe ning&uacute;n usuario con ese email', 
		'botonTexto' => 'Ir a p&aacute;gina principal'
	]);
	$PB->continue = false;
}

if($tsVerificar->verifyCode($email, $code, $type) || !is_array($tsData)) {
	$PB->page = 'aviso';
	$PB->ajax = 0;
	$smarty->assign("tsAviso", [
		'titulo' => 'Lo sientimos mucho', 
		'mensaje' => $tsData ?: 'La clave de validaci&oacute;n no es correcta'
	]);
	$PB->continue = false;
}

	//
if($PB->canContinue()) {
	$data = $tsData;
	if($type === 2){
		$smarty->assign('tsAviso', 
			$tsVerificar->accountActive((int)$data['user_id'], $code, $email)
		);
	} else {
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			$password = $tsCore->setSecure(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW));
			$smarty->assign('tsAviso', 
				$tsVerificar->changePassword((int)$data['user_id'], $password, $code, $email)
			);
		} else {
			$smarty->assign('tsAviso', [
				'titulo' => 'Actualizar contrase&ntilde;a', 
				'botonTexto' => 'Reestablecer contrase&ntilde;a',
				'botonLink' => 'submit'
			]);
		}
	}

	$smarty->assign('tsAction', $action);

}

if(!$PB->ajax())  {
	// Asignamos título
	$PB->assignDefaults();
	// Incluir footer
	require_once TS_ROOT . '/footer.php';
}