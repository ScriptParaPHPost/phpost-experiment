<?php

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

$PB = new PageBootstrap('posts', 0, $container->get('tsCore'), $container->get('tsSmarty'));

// PAGINA
$PB->page = "home";
$PB->title = $tsCore->settings['titulo'].' - '.$tsCore->settings['slogan'];

if($PB->canContinue()) {
	 
	// Category
	$category = $_GET['cat'] ?? '';

	$container->loader('utils/Cover.php', 'Cover', fn($c) => new Cover);

	// Home Class
	$tsHome = $container->loader('class/c.home.php', 'tsHome', fn($c) => 
		new tsHome($c->resolve('AppGlobal'), $c->get('Cover'))
	);

	// ULTIMOS POSTS
	$tsLastPosts = $tsHome->getNormalPosts($category);

	$smarty->assign("tsPosts", $tsLastPosts['data']);
	$smarty->assign("tsPages", $tsLastPosts['pages']);

	// ULTIMOS POSTS FIJOS, SOLO EN PRIMERA PÁGINA
	if((int)$tsLastPosts['current'] === 1) {
		$tsLastStickys = $tsHome->getStickyPosts($category, 5);
		$smarty->assign("tsPostsStickys", $tsLastStickys);
	}
	
	// ULTIMOS COMENTARIOS
	$tsComentarios = $container->loader(
		'class/c.comentarios.php', 'tsComentarios', 
		fn($c) => new tsComentarios($c->resolve('AppGlobal'), $c->get('Cover'))
	);
	$smarty->assign("tsComments", $tsComentarios->getLastComentarios());

	// TITULO
	if(!empty($category)) {
		$catData = $tsHome->getInfoCategory();
		$PB->title = $tsCore->settings['titulo'].' - '.$catData['c_nombre'];
		$smarty->assign("tsCatData", $catData);
	}
	// CAT
	$smarty->assign("tsCat", $category);
	  
	// CLASE ESTADISTICAS
	$tsEstadisticas = $container->loader(
		'class/c.estadisticas.php', 'tsEstadisticas', 
		fn($c) => new tsEstadisticas($c->resolve('Simple'))
	);
	// ESTADISTICAS
	$smarty->assign("tsStats", $tsEstadisticas->get());
	  
	// CLASE TOPS
	$tsTops = $container->loader(
		'class/c.tops.php', 'tsTops', 
		fn($c) => new tsTops($c->resolve('Tops'))
	);
	// TOP POSTS
	$smarty->assign("tsTopPosts", $tsTops->getHomeTopPosts('semana', false));
	// TOP USERS
	$smarty->assign("tsTopUsers", $tsTops->getHomeTopUsers('semana', false));

	// AFILIADOS
	$tsAfiliado = $container->loader('class/c.afiliado.php', 'tsAfiliado', fn($c) => new tsAfiliado($c->resolve('Afiliado')));
	// Referido?
	if(isset($_GET['ref'])) $tsAfiliado->urlIn();
	$smarty->assign("tsAfiliados", $tsAfiliado->getAfiliados());
	
}

if(!$PB->ajax())  {
	// Asignamos título
	$PB->assignDefaults();
	// Incluir footer
	require_once TS_ROOT . '/footer.php';
}