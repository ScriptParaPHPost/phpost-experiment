<div id="topsUserBox" class="border-blue-100 rounded shadow-sm mb-4">
	<div class="flex justify-between items-center p-2">
		<span class="font-semibold">TOPs usuarios</span>
		<a class="cursor-pointer square-xs aspect-square" href="{$tsRoutes.url}/top/usuarios/">
			<svg-icon name="trophy" class="square-xs"></svg-icon>
		</a>
	</div>
	<div class="box_cuerpo">
		<div class="filterByTabs flex justify-center items-center gap-3 my-2">
			<span role="button" onclick="TopsTabs('topsUserBox','Ayer','users')" id="Ayer" class="cursor-pointer">Ayer</span>
			<span role="button" onclick="TopsTabs('topsUserBox','Semana','users')" id="Semana" class="cursor-pointer font-bold">Semana</span>
			<span role="button" onclick="TopsTabs('topsUserBox','Mes','users')" id="Mes" class="cursor-pointer">Mes</span>
			<span role="button" onclick="TopsTabs('topsUserBox','Historico','users')" id="Historico" class="cursor-pointer">Hist&oacute;rico</span>
		</div>
		<ol id="filter" class="flex flex-col gap-2 px-3">
			{foreach from=$tsTopUsers key=i item=u}
				<li class="flex justify-start items-center mb-1">
					<a class="flex-grow truncate" href="{$tsConfig.url}/perfil/{$u.user_name}">{$u.user_name}</a>
					<span class="px-3">{$u.total}</span>
				</li>
			{/foreach}
		</ol>
	</div>
</div>