<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$tsTitle}</title>
<script src="{$tsRoutes.js}/jquery.min.js?{$smarty.now}"></script>
<script src="{$tsRoutes.js}/jquery.plugins.js?{$smarty.now}"></script>
<script src="{$tsRoutes.js}/acciones.js?{$smarty.now}"></script>
{if $tsUser->is_admod || $tsUser->permisos.moacp || $tsUser->permisos.most || $tsUser->permisos.moayca || $tsUser->permisos.mosu || $tsUser->permisos.modu || $tsUser->permisos.moep || $tsUser->permisos.moop || $tsUser->permisos.moedcopo || $tsUser->permisos.moaydcp || $tsUser->permisos.moecp}
<script src="{$tsRoutes.js}/moderacion.js?{$smarty.now}"></script>
{/if}
{if $tsConfig.c_allow_live}
<script src="{$tsRoutes.js}/live.js?{$smarty.now}"></script>
{/if}
<script>
var global_data = {
	user_key:'{$tsUser->uid}',
	postid:'{$tsPost.post_id}',
	fotoid:'{$tsFoto.foto_id}',
	img:'{$tsRoutes.images}/',
	smiles:'{$tsConfig.smiles}',
	url:'{$tsRoutes.url}',
	domain:'{$tsRoutes.domain}',
	s_title: '{$tsConfig.titulo}',
	s_slogan: '{$tsConfig.slogan}'
};
{if $tsNots > 0 && $tsMPs > 0 && $tsAction != 'leer'}
$(document).ready(() => {
{if $tsNots > 0}notifica.popup({$tsNots});{/if}
{if $tsMPs > 0 && $tsAction != 'leer'}mensaje.popup({$tsMPs});{/if}
});
{/if}
</script>
</head>
<body>
	{if $tsUser->is_admod == 1}{$tsConfig.install}{/if}
	<!--JAVASCRIPT-->
	<div id="loading" style="display:none">
		<img src="{$tsRoutes.images}/ajax-loader.gif" alt="Cargando"> Procesando...
	</div>

	<div id="swf"></div>
	<div id="js" style="display:none"></div>
	<div id="mask"></div>
	<div id="mydialog"></div>
	<div class="UIBeeper" id="BeeperBox"></div>

	<div id="brandday">
		<main>
			<!--MAIN CONTAINER-->
			<header>
				<div id="logo">
					<a id="logoi" title="{$tsConfig.titulo}" href="{$tsRoutes.url}">
						{$tsConfig.titulo}
					</a>
				</div>
				<div id="banner">
					{if $tsPage == 'posts' && $tsPost.post_id}
						{include file='m.global_search.tpl'}
					{else}
						{include file='m.global_ads_468.tpl'}
					{/if}
				</div>
			</header>
			<div id="contenido_principal">
				{include file='head_menu.tpl'}
				{include file='head_submenu.tpl'}
				{include file='head_noticias.tpl'}
				<section id="cuerpocontainer">
					<!--Cuperpo-->