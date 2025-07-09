<?php

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

if (!defined('PHPOST_CORE_LOADED')) 
	exit('Acceso denegado: ¡No puedes acceder este script directamente!');

// NIVELES DE ACCESO Y PLANTILLAS DE CADA ACCIÓN
$files = [
	'login-user' 	 => ['n' => 1, 'p' => ''],
	'login-activar' => ['n' => 1, 'p' => ''],
	'login-salir'	 => ['n' => 1, 'p' => '']
];

// REDEFINIR VARIABLES
$tsPage = 'php_files/p.login.'.$files[$action]['p'];
$tsLevel = $files[$action]['n'];
$tsAjax = empty($files[$action]['p']) ? 1 : 0;

// DEPENDE EL NIVEL
$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
if(!$tsLevelMsg) {
	echo '0: ' . $tsLevelMsg;
	die();
}

$tsAuthentication = $container->loader(
	'class/c.authentication.php', 'tsAuthentication', 
	fn($c) => new tsAuthentication($c->resolve('Authentication'))
);

// CODIGO |> 
switch($action){
	case 'login-user':
		header("Content-type: application/json; charset=utf-8");
		echo $tsAuthentication->loginUser(false);
	break;
	case 'login-activar':
		$activar = $tsAuthentication->userActivate();
		if($activar['user_password'])
			$tsAuthentication->loginUser('/cuenta/', $activar['user_nick'], $activar['user_password']);
		else {
			$tsPage = "aviso";
			$tsAjax = 0;
			$tsAviso = array('titulo' => 'Error al activar tu cuenta', 'mensaje' => 'El c&oacute;digo de validaci&oacute;n es incorrecto.');
			//
			$smarty->assign("tsAviso",$tsAviso);
		}
	break;
	case 'login-salir':
		echo $tsAuthentication->logoutUser($tsUser->uid, $tsCore->setRoutes('url'));
	break;
}