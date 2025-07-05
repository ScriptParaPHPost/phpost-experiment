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

$PB = new PageBootstrap('posts', 0, $container->get('tsCore'), $container->get('tsSmarty'));

$tsTitle = $tsCore->settings['titulo']; // TITULO DE LA PAGINA ACTUAL

if($PB->canContinue()) {

	// Afiliados
	$tsAfiliado = $container->loader(
		'class/c.afiliado.php', 'tsAfiliado', 
		fn($c) => new tsAfiliado($c->get('tsCore'), $c->get('tsUser'), $c->get('Junk'))
	);
	 
	// Posts Class
	$tsPosts = $container->loader(
		'class/c.posts.php', 'tsPosts', 
		fn($c) => new tsPosts($c->get('tsCore'), $c->get('tsUser'), $c->get('Junk'))
	);
		  
	// Referido?
	if(!empty($_GET['ref'])) $tsAfiliado->urlIn();
	 
	// Category
	$category = $_GET['cat'] ?? '';

	// Action
	$action = $_GET['action'] ?? '';
	 
	// Post anterior/siguiente
	if(in_array($action, ['next', 'prev', 'fortuitae'])) {
		$tsPosts->setNP();
	}

	if(!empty($_GET['post_id'])) {
		  
		// DATOS DEL POST
		$tsPost = $tsPosts->getPost();
		//
		if($tsPost['post_id'] > 0) {
			// TITULO NUEVO
			$tsTitle = $tsPost['post_title'].' - '.$tsTitle;
			// ASIGNAMOS A LA PLANTILLA
			$smarty->assign("tsPost", $tsPost);
			// DATOS DEL AUTOR
			$smarty->assign("tsAutor", $tsPosts->getAutor($tsPost['post_user']));
			// DATOS DEL RANGO DEL PUTEADOR						
			$smarty->assign("tsPunteador", $tsPosts->getPunteador());
			// RELACIONADOS
			$tsRelated = $tsPosts->getRelated($tsPost['post_tags']);
			$smarty->assign("tsRelated",$tsRelated);
			// COMENTARIOS
			$tsComments = $tsPosts->getComentarios($tsPost['post_id']);
			$smarty->assign("tsComments", [
				'num' => $tsComments['num'], 
				'data' => $tsComments['data']
			]);
			// PAGINAS
			$total = $tsPost['post_comments'];
			$tsPages = $tsCore->getPages($total, $tsCore->settings['c_max_com']);
			$tsPages['post_id'] = $tsPost['post_id'];
			$tsPages['autor'] = $tsPost['post_user'];
			//
			$smarty->assign("tsPages",$tsPages);
	 
		} else {
			//
			if($tsPost[0] === 'privado') {
				$PB->title = $tsPost[1].' - '.$tsTitle;
				$PB->page = "registro";
			} else {
				$PB->title = $tsTitle.' - '.$tsCore->settings['slogan'];
				$PB->page = "post.aviso";
				$PB->ajax = 0;
				//
				$title = str_replace("-",",",$tsCore->setSecure($_GET['title']));
				$title = explode(",",$title);
				// RELACIONADOS
				$smarty->assign("tsRelated", $tsPosts->getRelated($title));
			}
		}
	}

}

if(!$PB->ajax())  {
	// Asignamos título
	$PB->assignDefaults();
	// Incluir footer
	require_once TS_ROOT . '/footer.php';
}