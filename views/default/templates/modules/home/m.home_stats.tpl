<div id="webStats" class="border-blue-100 rounded shadow-sm mb-4">
	<div class="flex justify-between items-center p-2">
		<span class="font-semibold">Estad&iacute;sticas</span>
	</div>
	<div class="box_cuerpo px-4">
		<div class="grid grid-cols-2 gap-2">
			<a class="relative flex justify-start items-center text-right" href="{$tsRoutes.url}/usuarios/?online=true">
				<strong class="text-center" style="width:60px">{$tsStats.stats_online}</strong> <small class="block text-xs uppercase">online</small>
			</a>
			<a class="relative flex justify-start items-center text-right" href="{$tsRoutes.url}/usuarios/">
				<strong class="text-center" style="width:60px">{$tsStats.stats_miembros}</strong> <small class="block text-xs uppercase">miembros</small>
			</a>
			<div class="relative flex justify-start items-center text-right">
				<strong class="text-center" style="width:60px">{$tsStats.stats_posts}</strong> <small class="block text-xs uppercase">posts</small>
			</div>
			<div class="relative flex justify-start items-center text-right">
				<strong class="text-center" style="width:60px">{$tsStats.stats_fotos}</strong> <small class="block text-xs uppercase">fotos</small>
			</div>
		</div>
		<small class="block text-center text-xs py-3">Actualizado: {$tsStats.stats_time|hace:true}</small>
	</div>
</div>