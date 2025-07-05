<div id="topsPostBox" class="border-blue-100 rounded shadow-sm mb-4">
	<div class="flex justify-between items-center p-2">
		<span class="font-semibold">TOPs posts</span>
		<a class="cursor-pointer square-xs aspect-square" href="{$tsRoutes.url}/top/">
			<svg-icon name="trophy" class="square-xs"></svg-icon>
		</a>
	</div>
	<div class="box_cuerpo">
		<div class="filterByTabs flex justify-center items-center gap-3 my-2">
			<span role="button" onclick="TopsTabs('topsPostBox','Ayer')" id="Ayer" class="cursor-pointer">Ayer</span>
			<span role="button" onclick="TopsTabs('topsPostBox','Semana')" id="Semana" class="cursor-pointer font-bold">Semana</span>
			<span role="button" onclick="TopsTabs('topsPostBox','Mes')" id="Mes" class="cursor-pointer">Mes</span>
			<span role="button" onclick="TopsTabs('topsPostBox','Historico')" id="Historico" class="cursor-pointer">Hist&oacute;rico</span>
		</div>
		<ol id="filter" class="flex flex-col gap-2 px-3">
			{foreach from=$tsTopPosts key=i item=p}
				<li class="flex justify-start items-center mb-1">
					<a class="flex-grow truncate" href="{$p.post_url}">{$p.post_title}</a>
					<span class="px-3">{$p.post_puntos}</span>
				</li>
			{/foreach}
		</ol>
	</div>
</div>