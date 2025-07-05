<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
<meta charset="UTF-8">
<title>{$tsTitle}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="{$tsRoutes.images}/favicon16.png" rel="icon" type="image/png" sizes="16x16">
<link href="{$tsRoutes.images}/favicon32.png" rel="icon" type="image/png" sizes="32x32">
<link href="{$tsRoutes.images}/favicon64.png" rel="icon" type="image/png" sizes="64x64">
<link href="{$tsRoutes.images}/favicon16.png" rel="shortcut icon" type="image/png">
<link href="{$tsRoutes.tema}/phpostv4.css?{$smarty.now}" rel="stylesheet" type="text/css" />
<link href="{$tsRoutes.tema}/utils.css?{$smarty.now}" rel="stylesheet" type="text/css" />
{include "script.line.tpl"}
{if $tsAction =='registro'}
<script>const siteKey = '{$reCAPTCHA_site_key}';</script>
<script src="https://www.google.com/recaptcha/api.js?render={$reCAPTCHA_site_key}"></script>
{/if}
<script src="{$tsRoutes.js}/app.js?{$smarty.now}" type="module" defer></script>
{if $tsConfig.c_allow_live}
<script src="{$tsRoutes.js}/live.js?{$smarty.now}" type="module" defer></script>
{/if}
<script src="{$tsRoutes.js}/{$tsPage}.js?{$smarty.now}" type="module" defer></script>
</head>
<body class="bg-neutral-100 text-neutral-700 dark:bg-neutral-900 dark:text-neutral-100">

	<div id="app" class="relative">
		{include "load.header.tpl"}
		<main class="container">
			<section id="body">