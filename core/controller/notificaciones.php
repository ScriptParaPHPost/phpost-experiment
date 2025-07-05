<?php 
/**
 * Controlador
 *
 * @name    notificaciones.php
 * @author  PHPost Team
*/


include __DIR__ . "/../../header.php"; // INCLUIR EL HEADER
$PB = new PageBootstrap('notificaciones', 2, $container->get('tsCore'), $container->get('tsSmarty'));

$tsTitle = $tsCore->settings['titulo'].' - '.$tsCore->settings['slogan']; 	// TITULO DE LA PAGINA ACTUAL

if($PB->canContinue()) {

	$action = htmlspecialchars($_GET['action']);

	if(empty($action)){
      $tsNotificaciones->show_type = 2;
		$notificaciones = $tsNotificaciones->getNotificaciones();
		$smarty->assign("tsData",$notificaciones);
      // LIVE SOUND
      $smarty->assign("tsStatus",$_COOKIE);
   } else {
		$smarty->assign("tsData",$tsNotificaciones->getFollows($action));
	}
	$smarty->assign("tsAction",$action);
}

if(!$PB->ajax())  {
	// Asignamos título
	$PB->assignDefaults();
	// Incluir footer
	require_once TS_ROOT . '/footer.php';
}