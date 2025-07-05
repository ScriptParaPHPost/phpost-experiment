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


// Página solicitada
$smarty->assign("tsPage", $PB->page());

$smarty->page = $PB->page();

// Cargamos los directorios
$smarty->loaderTemplates();
$smarty->loader();