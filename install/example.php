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

return [

	// Configuración general de la aplicación
	'app' => [
		// Nombre de la aplicación
		'name'		=> 'PHPost Experiment',
		// Habilita el modo debug (para desarrollo, muestra errores detallados)
		'debug'		=> true,
		// Ruta al archivo de log donde se guardarán los errores o mensajes del sistema
		'log'			=> '/storage/logs/phpost.log',
		// Zona horaria del servidor (muy importante para registros de tiempo)
		'timezone'	=> 'America/Argentina/Buenos_Aires',
		// Localización e idioma predeterminado (formato de fechas, traducciones, etc.)
		'locale'		=> 'es_ES',
		// Versión actual del sistema
		'version'	=> '4.0.0'
	],

	// Configuración de la base de datos
	'db' => [
		// Dirección del servidor de base de datos (por lo general, 'localhost' o IP)
		'hostname' => '{{hostname}}',
		// Usuario con permisos para conectarse a la base de datos
		'username' => '{{username}}',
		// Contraseña del usuario de base de datos
		'password' => '{{password}}',
		// Nombre de la base de datos que se va a utilizar
		'database' => '{{database}}',
		// Codificación de caracteres a utilizar (utf8mb4 permite emojis y caracteres especiales)
		'charset' => 'utf8mb4'
	],

	// Configuración para el envío básico de correos usando mail()
	'mail' => [
	   // Dirección predeterminada del remitente
	   'from' => 'no-reply@{{domain}}',
	   // Nombre del remitente (opcional, para el encabezado 'From')
	   'from_name' => '{{titulo}}',
	   // Dirección donde se recibirán los correos de contacto o errores
	   'admin' => '{{email}}',
	   // Asunto por defecto si no se especifica otro
	   'default_subject' => 'Sistema de Notificaciones de {{titulo}}',
	   // Codificación del correo
	   'encoding' => 'UTF-8',
	   // Formato por defecto: 'text' o 'html'
	   'content_type' => 'text/html'
	],

	// Entorno de desarrollo: 
	// true = desarrollo (muestra errores, logs detallados)
	// false = producción (oculta errores, más seguro)
	'dev' => true

];