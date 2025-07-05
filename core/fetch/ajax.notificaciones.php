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

$PB = new PageBootstrap('notificaciones', 2, $tsCore, $smarty);

// NIVELES DE ACCESO Y PLANTILLAS DE CADA ACCIÓN
$files = [
	'notificaciones-ajax' => ['n' => 2, 'p' => 'ajax'],
	'notificaciones-filtro' => ['n' => 2, 'p' => ''],
];

// REDEFINIR VARIABLES
$PB->page = 'php_files/p.notificaciones.'.$files[$action]['p'];
$tsLevel = $files[$action]['n'];
$PB->ajax = empty($files[$action]['p']) ? 1 : 0;
//
$how = $_POST['action'];

$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
if(!$tsLevelMsg) { 
	echo '0: '.$tsLevelMsg; 
	die();
}

// CODIGO
switch($action){
	case 'notificaciones-ajax':
		$tsAjax = 1; // AJAX
		switch($how){
			case 'last':
				// <--
				$tsAjax = 0; // AJAX
				$notificaciones = $tsNotificaciones->getNotificaciones();
				$smarty->assign("tsData", $notificaciones['data']);
				// -->
			break;
			case 'follow':
				echo $tsNotificaciones->setFollow();
			break;
			case 'unfollow':
				echo $tsNotificaciones->setUnFollow();
			break;
			case 'spam':
				echo $tsNotificaciones->setSpam();
			break;
		}
	break;
	case 'notificaciones-filtro':
		echo $tsNotificaciones->setFiltro();
	break;
}
// HACK xD
$_GET['ts'] = true;