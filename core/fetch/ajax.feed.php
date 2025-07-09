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

$files = [
   'feed-script-version' => ['n' => 1, 'p' => ''],
   'feed-version' => ['n' => 1, 'p' => ''],
   'feed-support' => ['n' => 1, 'p' => ''],
];

// REDEFINIR VARIABLES
$tsPage = 'php_files/p.live.'.$files[$action]['p'];
$tsLevel = $files[$action]['n'];
$tsAjax = empty($files[$action]['p']) ? 1 : 0;

/**********************************\

*	(INSTRUCCIONES DE CODIGO)		*

\*********************************/
	
// DEPENDE EL NIVEL
$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
if(!$tsLevelMsg) { 
	echo '0: '.$tsLevelMsg['mensaje']; 
	die();
}

//
$code = [
	'title' => $tsCore->settings['titulo'], 
	'slogan' => $tsCore->settings['slogan'], 
	'url' => $tsCore->settings['url'], 
	'version' => $tsCore->settings['version_code'], 
	'admin' => $tsUser->nick, 
	'id' => $tsUser->uid
];
$key = base64_encode(serialize($code));

$Config = $container->get('Config');

$version_now = 'PHPost v' . $Config->get('version');
$version_code = 'phpost_v' . str_replace('.', '_', $Config->get('version'));

// CODIGO
switch($action) {
	case 'feed-support':
		$json = $tsCore->getUrlContent('http://www.phpost.net/feed/index.php?type=support&key='.$key);
		echo $json;
	break;
	case 'feed-version':
		/**
		 * Versión del 20 de junio de 2025 *
		 * PHPost v4.x *
		 */
		# ACTUALIZAR VERSIÓN
		if($tsCore->settings['version'] !== $version_now) {
			$now = time();
			db_exec([__FILE__, __LINE__], 'query', "UPDATE `w_config_misc` SET version = '$version_now', version_code = '$version_code' WHERE tscript_id = 1");
			db_exec([__FILE__, __LINE__], 'query', "UPDATE `w_stats` SET stats_time_upgrade = $now WHERE stats_no = 1");
		}
		$json = $tsCore->getUrlContent('http://www.phpost.net/feed/index.php?type=version&key='.$key);
		echo $json;
	break;
	default:
		die('0: Este archivo no existe.');
	break;
}