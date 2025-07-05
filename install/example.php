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

	'db' => [

		# ['hostname'] The hostname of your database server.
		'hostname' => 'dbhost',

		# ['username'] The username used to connect to the database
		'username' => 'dbuser',

		# ['password'] The password used to connect to the database
		'password' => 'dbpass',

		# ['database'] The name of the database you want to connect to
		'database' => 'dbname',

		'charset' => 'utf8mb4'

	],

	# false = Production
	# true = Development
	'dev' => true

];