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
	'registro-form' 		 	=> ['n' => 1, 'p' => 'form'],
	'registro-check-nick' 	=> ['n' => 1, 'p' => ''],
	'registro-check-email' 	=> ['n' => 1, 'p' => ''],	
	'registro-geo' 			=> ['n' => 0, 'p' => ''],
	'registro-nuevo' 			=> ['n' => 1, 'p' => ''],
];

// REDEFINIR VARIABLES
$tsPage = 'php_files/p.registro.'.$files[$action]['p'];
$tsLevel = $files[$action]['n'];
$tsAjax = empty($files[$action]['p']) ? 1 : 0;

// DEPENDE EL NIVEL
$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
if(!$tsLevelMsg) { 
	echo '0: '.$tsLevelMsg; 
	die();
}
	
// CLASE
$tsRegistro = $container->loader('class/c.registro.php', 'tsRegistro', 
	fn($c) => new tsRegistro($c->resolve('Registro'))
);
	
// CODIGO
switch($action) {
	case 'registro-form':
		if((int)$tsCore->settings['c_reg_active'] === 0) {
			$tsAjax = '1';
			echo '0: <div class="dialog_box">El registro de nuevas cuentas en <b>'.$tsCore->settings['titulo'].'</b> est&aacute; desactivado.</div>';
		} else {
			include TS_EXTRA . "datos.php";
			// SOLO MENORES DE 100 AÑOS xD Y MAYORES DE...
			$now_year = date("Y",time());
			$max_year = 100 - $tsCore->settings['c_allow_edad'];
			$end_year = $now_year - $tsCore->settings['c_allow_edad'];
			$smarty->assign("tsMax", $max_year);
			$smarty->assign("tsEndY", $end_year);
			$smarty->assign("tsPaises", $tsPaises);
			$smarty->assign("tsMeses", $tsMeses);	
		}
	break;
	case 'registro-check-nick':	
	case 'registro-check-email':
		echo json_encode($tsRegistro->checkUserEmail());
	break;
	case 'registro-geo':
		include("../ext/geodata.php");
		$pais = htmlspecialchars($_GET['pais_code']);
		if($pais) $html = '1: ';
		else $html = '0: El campo <b>pais_code</b> es requerido para esta operacion';
		foreach($estados[$pais] as $key => $estado){
			$html .= '<option value="'.($key+1).'">'.$estado.'</option>'."\n";
		}
		if(strlen($html) > 3) echo $html;
		else echo '0: Código de pais incorrecto.';
	break;
	case 'registro-nuevo':
		echo json_encode($tsRegistro->newUserRegister());
	break;
}