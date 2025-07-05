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
	'tops-posts-filter' => ['n' => 1, 'p' => ''],
	'tops-users-filter' => ['n' => 1, 'p' => ''],
];

// REDEFINIR VARIABLES
$tsPage = 'php_files/p.tops.'.$files[$action]['p'];
$tsLevel = $files[$action]['n'];
$tsAjax = empty($files[$action]['p']) ? 1 : 0;

// DEPENDE EL NIVEL
$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
if(!$tsLevelMsg) { 
	echo '0: '.$tsLevelMsg['mensaje']; 
	die();
}

// CLASE
$tsTops = $container->loader(
	'class/c.tops.php', 'tsTops', 
	fn($c) => new tsTops($c->resolve('Tops'))
);

$filter = strtolower($tsCore->setSecure(filter_input(INPUT_POST, 'filter', FILTER_UNSAFE_RAW)));

// CODIGO
switch($action){
	case 'tops-posts-filter':
		//<--
		echo $tsTops->getHomeTopPosts($filter);
		//-->
	break;
	case 'tops-users-filter':
		//<--
		echo $tsTops->getHomeTopUsers($filter);
		//-->
	break;
}