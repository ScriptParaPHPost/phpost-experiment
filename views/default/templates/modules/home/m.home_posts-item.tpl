<div class="mb-3 rounded flex justify-center items-start gap-2 overflow-hidden shadow-sm post-status relative{if $p.post_sponsored} border-blue-300{/if}" data-box="item" data-status="{if $p.post_sponsored}sponsored{elseif $p.post_private}private{elseif $p.user_baneado == 1}baneado{elseif $p.user_activo == 0}activo{elseif $p.post_status > 0}status{$p.post_status}{else}empty{/if}" style="--altura: {if $p.post_sticky}5{else}9{/if}0px;">

	<div class="flex-grow-0">
		<div class="bg-blue-200 bg-opacity:70 rounded overflow-hidden" style="width:{if $p.post_sticky}var(--altura){else}100px{/if};height: var(--altura);">
			<img src="{$tsRoutes.images}/favicon64.png" data-src="{$p.post_portada}" alt="{$p.post_title}" class="w-full h-full object-cover">
		</div>
	</div>

	<div class="flex-grow basis-3/4 flex justify-center items-start gap-1 flex-col" style="height: var(--altura);">
		<a href="{$p.post_url}" class="truncate font-semibold">{$p.post_title}</a>
		{if !$p.post_sticky}
			<div class="flex justify-start items-center gap-2 text-sm">
				<a href="{$p.post_categoria}" class="badge p-1">
					<img src="{$tsRoutes.images}/categorias/{$p.c_img}" alt="Categoria {$p.c_nombre}" width="16" height="16">
				</a>
				<span>Por <a href="{$p.post_user}" title="Perfil de {$p.user_name}" class="font-semibold">@{$p.user_name}</a>,</span>
				<time>{$p.post_date|hace:true}</time>
				{*
					<span>Puntos <strong>{$p.post_puntos}</strong></span>
					<span>Comentarios <strong>{$p.post_comments}</strong></span>
					<span>Categoría <a href="{$p.post_categoria}" title="Categoría {$p.c_nombre}">{$p.c_nombre}</a></span>
				*}
			</div>
		{/if}
	</div>
	{if $p.post_status > 0 || $p.user_activo == 0 || $p.user_baneado == 1}
		<small class="absolute right-2 bottom-1 text-xs">{if $p.post_status == 3}El post est&aacute; en revisi&oacute;n{elseif $p.post_status == 1}El post se encuentra en revisi&oacute;n por acumulaci&oacute;n de denuncias{elseif $p.post_status == 2}El post est&aacute; eliminado{elseif $p.user_activo == 0}La cuenta del usuario est&aacute; desactivada{/if}</small>
	{/if}
	{if $p.post_sticky}<img src="{$tsRoutes.images}/favicon16.png" alt="post fijado staff {$p.c_nombre}" />{/if}
</div>