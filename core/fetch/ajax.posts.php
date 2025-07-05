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
	'posts-genbus' 			 => ['n' => 2, 'p' => 'genbus'],
	'posts-preview' 			 => ['n' => 2, 'p' => 'preview'],
	'posts-borrar' 			 => ['n' => 2, 'p' => ''],
	'posts-admin-borrar' 	 => ['n' => 2, 'p' => ''],
	'posts-votar' 				 => ['n' => 2, 'p' => ''],
	'posts-last-comentarios' => ['n' => 0, 'p' => 'last-comentarios'],
];

// REDEFINIR VARIABLES
$tsPage = 'php_files/p.posts.'.$files[$action]['p'];
$tsLevel = $files[$action]['n'];
$tsAjax = empty($files[$action]['p']) ? 1 : 0;

// DEPENDE EL NIVEL
$tsLevelMsg = $tsCore->setLevel($tsLevel, true);
if(!$tsLevelMsg) { 
	echo '0: '.$tsLevelMsg['mensaje']; 
	die();
}

// CLASE
$tsPosts = $container->loader('class/c.posts.php', 'tsPosts', fn($c) => 
	new tsPosts($c->resolve('AppGlobal'))
);

// CODIGO
switch($action){
	case 'posts-genbus':
		//<--
		$do = htmlspecialchars($_GET['do']);
		$q = $tsCore->setSecure($_POST['q']);
		//
		if($do == 'search'){
			$smarty->assign("tsPosts",$tsPosts->simiPosts($q));   
		}elseif($do == 'generador'){
			$tags = $tsPosts->genTags($q);
			$smarty->assign("tsTags",$tags);
		}
		//
		$smarty->assign("tsDo",$do);
		//-->
	break;
	case 'posts-preview':
		//<--
		$smarty->assign("tsPreview",$tsPosts->getPreview());
		//-->
	break;
	case 'posts-borrar':
		//<--
		echo $tsPosts->deletePost();
		//-->
	break;
	case 'posts-admin-borrar':
		//<--
		echo $tsPosts->deleteAdminPost();
		//-->
	break;
	case 'posts-votar':
		//<--
		echo $tsPosts->votarPost();
		//-->
	break;
	case 'posts-last-comentarios':
		//<--
		$tsComentarios = $container->loader(
			'class/c.comentarios.php', 'tsComentarios', 
			fn($c) => new tsComentarios($c->resolve('AppGlobal'))
		);
		$PB = new PageBootstrap('acceso', 1, $container->get('tsCore'), $container->get('tsSmarty'));
		$PB->page = $tsPage;
		$smarty->assign("tsComments", $tsComentarios->getLastComentarios());
		//-->
	break;
}