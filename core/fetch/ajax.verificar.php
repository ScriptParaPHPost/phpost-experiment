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
	'verificar-account'  => ['n' => 1, 'p' => ''],
	'verificar-password' => ['n' => 1, 'p' => '']
];

// REDEFINIR VARIABLES
$tsPage = 'php_files/p.verificar.'.$files[$action]['p'];
$tsLevel = $files[$action]['n'];
$tsAjax = empty($files[$action]['p']) ? 1 : 0;

// DEPENDE EL NIVEL
$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
if(!$tsLevelMsg) {
	echo '0: ' . $tsLevelMsg;
	die();
}

if (!isset($action)) {
	echo '0: Acción no válida.';
	die();
}

$tsVerificar = $container->loader(
	'class/c.verificar.php', 'tsVerificar', 
	fn($c) => new tsVerificar($c->resolve('Verificar'))
);

$email = $tsCore->setSecure(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

if(!is_array($tsData = $tsVerificar->verifyEmail($email, $action))) {
	echo $tsData;
	die;
}

$key = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$tsData['user_email'] = $email;

$type = ($action === 'verificar-password') ? 1 : 2;
$page = ((int)$type === 1) ? 'password' : 'validar';
$message = ((int)$type === 1)
	? "Las intrucciones para recuperar su contrase&ntilde;a de $email, si no aparece el e-mail en su bandeja de entrar, revise en correo no deseado porque puede haberse filtrado."
	: "Te hemos enviado un correo a $email con los pasos para continuar.\n\nSi en los proximos minutos no lo encuentras en tu bandeja de entrada, por favor, revisa tu carpeta de correo no deseado, es posible que se haya filtrado.\n\nMuchas gracias";


if($tsVerificar->verifyToDB((int)$tsData['user_id'], $email, $type, $key)) {
	echo $tsVerificar->sendEmail($tsData, [
		'page' => $page,
		'type' => $type,
		'key' => $key,
		'plantilla' => ((int)$type === 1) ? 'recuperar_contrasena' : 'activar_cuenta_email',
		'asunto' => ((int)$type === 1) ? 'Recuperar Contraseña' : 'Active su cuenta',
		'mensaje' => $message
	]);
}