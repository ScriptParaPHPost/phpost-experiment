<div id="lastCommBox" class="border-blue-100 rounded shadow-sm mb-4">
	<div class="flex justify-between items-center p-2">
		<span class="font-semibold">&Uacute;ltimos comentarios</span>
		<span role="button" class="cursor-pointer square-xs aspect-square" title="Recargar comentarios" onclick="actualizar_comentarios();">
			<svg-icon name="renew" class="square-xs"></svg-icon>
		</span>
	</div>
	<div class="flex flex-col gap-1" id="ultimos_comentarios">
		{include "p.posts.last-comentarios.tpl"}
	</div>
</div>