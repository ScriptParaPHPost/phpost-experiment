<?php

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

require_once __DIR__ . '/bootstrap.php';

# Cargamos el container
require_once __DIR__ . '/core/utils/Container.php';
$container = new Container();

// Cargamos directamente
require_once $container->loaderPHP('extras/functions.php');

// ./core/utils/Junk.php
// $Junk = new Junk;
$containers = [
	['file' => 'utils/Avatar.php', 'key' => 'Avatar'],
	['file' => 'utils/Junk.php', 'key' => 'Junk'],
	['file' => 'utils/LimpiarSolicitud.php', 'key' => 'LimpiarSolicitud'],
	['file' => 'utils/Paginator.php', 'key' => 'Paginator'],
	['file' => 'utils/PasswordManager.php', 'key' => 'PasswordManager']
];
$container->multiLoader($containers);
$container->get('LimpiarSolicitud')->clear();
$container->get('LimpiarSolicitud')->run();

$tsCore = $container->loader('class/c.core.php', 'tsCore', fn() => new tsCore);

$tsSession = $container->loader('class/c.session.php', 'tsSession', fn($c) => new tsSession($c->get('tsCore')));

$tsUser = $container->loader('class/c.user.php', 'tsUser', fn($c) => new tsUser($c->resolve('User')));

$tsNotificaciones = $container->loader('class/c.notificaciones.php', 'tsNotificaciones', 
	fn($c) => new tsNotificaciones($c->get('tsUser'))
);

$tsActividad = $container->loader('class/c.actividad.php', 'tsActividad', fn($c) => new tsActividad($c->get('tsUser')));

$tsMP = $container->loader(
	'class/c.mensajes.php', 'tsMensajes', 
	fn() => new tsMensajes()
);

// Definimos el template a utilizar
define('TS_TEMA', $tsCore->getTema('t_path') ?? 'default');

$smarty = $container->loader(
	'class/c.smarty.php', 'tsSmarty', 
	fn() => new tsSmarty(TS_TEMA)
);

// Nueva configuración
$smarty->output(false);

// Cargamos un manejador de página
require_once $container->loaderPHP('utils/PageBootstrap.php');

/*
 * -------------------------------------------------------------------
 *  Asignación de variables
 * -------------------------------------------------------------------
*/
$configuraciones = [
	'tsConfig' => $tsCore->settings,
	'tsRoutes' => $tsCore->setRoutes(),
	'tsCategorias' => $tsCore->getCategorias(),
	'tsUser' => $tsUser,
	'tsAvisos' => $tsNotificaciones->avisos,
	'tsNots' => $tsNotificaciones->notificaciones,
	'tsMPs' => $tsMP->mensajes,
	'tsAppVersion' => file_get_contents(__DIR__ . '/storage/.version')
];
// Configuraciones
$smarty->assign($configuraciones);
			
/**
 * Si hay alguna IP bloqueada por el Moderador/Administrador,
 * ejecutamos esta función, en caso contrario no hará nada
*/
ip_banned();

/**
 * Si hay un usuario baneado por el Moderador/Administrador,
 * ejecutamos esta función, en caso contrario no hará nada
*/
user_banned();

/**
 * Si la página esta en modo mantenimiento, ejecutamos la función
*/
site_in_maintenance();