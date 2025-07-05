<?php

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

# Comprobamos que exista el archivo config.inc.php
if(!file_exists(__DIR__ . '/storage/config.inc.php')) {
	header("Location: ./install/index.php");
	exit;
}

define('PHPOST_CORE_LOADED', true);

// Incluimos header
include __DIR__ . '/header.php';

// Cargamos la página
$section = htmlspecialchars(filter_input(INPUT_GET, 'section', FILTER_UNSAFE_RAW)) ?? 'posts';

// Home
include_once __DIR__ . '/core/controller/'.$section.'.php';