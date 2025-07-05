<?php

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

/**
 * Bootstrap.php
 * 
 * Archivo para inicializar todo
*/

if( !defined('PHPOST_CORE_LOADED') ) {
	define('PHPOST_CORE_LOADED', TRUE);
}

// Sesión
if(!isset($_SESSION)) {
	session_name('PHPOST_SESSION_V4');
	session_start();
}

// true = Modo desarrollo
// false = Modo producción
$isDev = true;

// Reporte de errores
ini_set('display_errors', $isDev ? '1' : '0');

// Activamos para registrar errores
ini_set('log_errors', '1');

// Archivo donde se registran los errores
ini_set('error_log', __DIR__ . '/storage/logs/phpost.log');

// Mostramos los errores
error_reporting($isDev ? E_ALL : E_ALL ^ E_WARNING ^ E_NOTICE);

// Límite de ejecución
set_time_limit(300);

//DEFINICION DE CONSTANTES
define('TS_ROOT', __DIR__);

define('TS_CLASS', TS_ROOT . '/core/class/');

define('TS_EXTRA', TS_ROOT . '/core/extras/');

define('TS_FILES', TS_ROOT . '/storage/');

set_include_path(get_include_path() . PATH_SEPARATOR . realpath('./'));