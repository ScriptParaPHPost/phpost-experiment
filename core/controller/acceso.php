<?php

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

require_once __DIR__ . '/../../header.php';

$PB = new PageBootstrap('acceso', 1, $container->get('tsCore'), $container->get('tsSmarty'));

$page = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW) ?? '';
$PB->title = ($page === 'iniciar' ? "Bienvenidos a " : "Crea tu cuenta en ") . $tsCore->settings['titulo'];

if($tsUser->is_member) header("Location: {$tsCore->setRoutes('url')}");

if($PB->canContinue()) {

	if($page === 'registro') {
		
		$PB->page = 'registro';

		$Publickey = $container->get('Junk')->setKeys();

	   $smarty->assign([
	      "ApiUrl" => "https://google.com/recaptcha/api.js?render=" . $Publickey,
	      "reCAPTCHA_site_key" => $Publickey,
	      "tsAbierto" => $tsCore->settings["c_reg_active"]
	   ]);
		
	} else {
		$PB->page = 'login';
	}

	$smarty->assign('tsAction', $page);

}

if(!$PB->ajax())  {
	// Asignamos título
	$PB->assignDefaults();
	// Incluir footer
	require_once TS_ROOT . '/footer.php';
}