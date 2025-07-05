<div class="flex-grow-1 md:absolute lg:relative" id="main_menu">
	<nav aria-label="Menú principal" class="md:block lg:flex justify-start items-start gap-3">

		{if $tsConfig.c_allow_portal && $tsUser->is_member}
			<div class="relative">
				<a href="{$tsRoutes.url}/portal" aria-label="Acceder al portal del sitio" class="hover:bg-blue-100 dark:hover:bg-blue-900 flex justify-start items-start gap-2 py-2 px-3 rounded{if $tsPage == 'portal'} bg-blue-200 dark:bg-blue-900{/if}">
					<svg-icon name="shield-users" class="square-xs"></svg-icon>
					<span class="font-semibold">Portal</span>
				</a>
			</div>
		{/if}

		<div class="relative">
			<a href="{$tsRoutes.url}/posts/" aria-label="Acceder a los posts" name="posts" class="hover:bg-blue-100 dark:hover:bg-blue-900 flex justify-start items-start gap-2 py-2 px-3 rounded{if in_array($tsPage, ['','home','posts'])} bg-blue-200 dark:bg-blue-900{/if}">
				<svg-icon name="book-open" class="square-xs pointer-events-none"></svg-icon>
				<span class="font-semibold pointer-events-none">Posts</span>
			</a>
			<div class="absolute mt-3 bg-neutral-100 dark:bg-neutral-900 rounded shadow p-2 dropoff z-10" data-target="posts" style="min-width: 200px;">
				<a class="hover:bg-blue-100 dark:hover:bg-blue-900 font-normal block p-2 mb-2 rounded{if in_array($tsPage, ['','home','posts'])} bg-blue-100 dark:bg-blue-900{/if}" href="{$tsRoutes.url}/posts/">Inicio</a>
				<a class="hover:bg-blue-100 dark:hover:bg-blue-900 font-normal block p-2 mb-2 rounded{if $tsPage == 'buscador'} bg-blue-100 dark:bg-blue-900{/if}" href="{$tsRoutes.url}/buscador/">Buscador</a>
            {if $tsUser->is_member}
	            {if $tsUser->is_admod || $tsUser->permisos.gopp}
	            	<!-- Tiene permisos para postear o es admin -->
						<a class="hover:bg-blue-100 dark:hover:bg-blue-900 font-normal block p-2 mb-2 rounded{if $tsSubmenu == 'agregar'} bg-blue-100 dark:bg-blue-900{/if}" href="{$tsRoutes.url}/agregar/">Agregar nuevo post</a>
					{/if}
					<a class="hover:bg-blue-100 dark:hover:bg-blue-900 font-normal block p-2 mb-2 rounded{if $tsPage == 'mod-history'} bg-blue-100 dark:bg-blue-900{/if}" href="{$tsRoutes.url}/mod-history/">Historial</a>
					{if $tsUser->is_admod || $tsUser->permisos.moacp}
						<!-- Es admin o tiene permisos especiales? -->
						<a class="hover:bg-blue-100 dark:hover:bg-blue-900 font-normal block p-2 mb-0 rounded flex justify-between items-center" href="{$tsRoutes.url}/moderacion/">Moderaci&oacute;n{if $tsConfig.c_see_mod && $tsConfig.novemods.total} <span class="bg-blue-200 dark:bg-blue-900 block rounded-sm px-2 flex justify-center items-center text-xs font-bold">{$tsConfig.novemods.total}</span>{/if}</a>
					{/if}
				{/if}
			</div>
		</div>

		{if !$tsConfig.c_fotos_private && $tsUser->is_member}
		<div class="relative">
			<a href="{$tsRoutes.url}/fotos/" aria-label="Acceder a las fotos" name="fotos" class="hover:bg-blue-100 dark:hover:bg-blue-900 flex justify-start items-start gap-2 py-2 px-3 rounded{if $tsPage == 'fotos'} bg-blue-200 dark:bg-blue-900{/if}">
				<svg-icon name="images" class="square-xs pointer-events-none"></svg-icon>
				<span class="font-semibold pointer-events-none">Fotos</span>
			</a>
			<div class="absolute mt-3 bg-neutral-100 dark:bg-neutral-900 rounded shadow p-2 dropoff z-10" data-target="fotos" style="min-width: 200px;">
				<a class="hover:bg-blue-100 dark:hover:bg-blue-900 font-normal block p-2 mb-2 rounded{if $tsAction == '' && $tsPage == 'fotos'} bg-blue-100 dark:bg-blue-900{/if}" href="{$tsRoutes.url}/fotos/">Inicio</a>
				{if $tsAction == 'album' && $tsFUser.0 != $tsUser->uid}
					<a class="hover:bg-blue-100 dark:hover:bg-blue-900 font-normal block p-2 mb-2 rounded{if $tsAction == 'album' && $tsPage == 'fotos'} bg-blue-100 dark:bg-blue-900{/if}" href="{$tsConfig.url}/fotos/{$tsFUser.1}">&Aacute;lbum de {$tsFUser.1}</a>
				{/if}
				{if $tsUser->is_admod || $tsUser->permisos.gopf}
					<a class="hover:bg-blue-100 dark:hover:bg-blue-900 font-normal block p-2 mb-2 rounded{if $tsAction == 'agregar' && $tsPage == 'fotos'} bg-blue-100 dark:bg-blue-900{/if}" href="{$tsConfig.url}/fotos/agregar.php">Agregar Foto</a>
				{/if}
				<a class="hover:bg-blue-100 dark:hover:bg-blue-900 font-normal block p-2 mb-2 rounded{if $tsAction == 'album' && $tsFUser.0 == $tsUser->uid} bg-blue-100 dark:bg-blue-900{/if}" href="{$tsConfig.url}/fotos/{$tsUser->nick}">Mis Fotos</a>
			</div>
		</div>
		{/if}

		<div class="relative">
			<a href="{$tsRoutes.url}/top/" aria-label="Acceder a los tops" name="tops" class="hover:bg-blue-100 dark:hover:bg-blue-900 flex justify-start items-start gap-2 py-2 px-3 rounded{if $tsPage == 'tops'} bg-blue-200 dark:bg-blue-900{/if}">
				<svg-icon name="trophy" class="square-xs pointer-events-none"></svg-icon>
				<span class="font-semibold pointer-events-none">Tops</span>
			</a>
			<div class="absolute mt-3 bg-neutral-100 dark:bg-neutral-900 rounded shadow p-2 dropoff z-10" data-target="tops" style="min-width: 200px;">
				<a class="hover:bg-blue-100 dark:hover:bg-blue-900 font-normal block p-2 mb-2 rounded{if $tsPage == 'tops'} bg-blue-100 dark:bg-blue-900{/if}" href="{$tsRoutes.url}/top/">Inicio</a>
				<a class="hover:bg-blue-100 dark:hover:bg-blue-900 font-normal block p-2 mb-2 rounded{if $tsAction == 'posts'} bg-blue-100 dark:bg-blue-900{/if}" href="{$tsRoutes.url}/top/posts/">Posts</a>
				<a class="hover:bg-blue-100 dark:hover:bg-blue-900 font-normal block p-2 mb-2 rounded{if $tsAction == 'usuarios'} bg-blue-100 dark:bg-blue-900{/if}" href="{$tsRoutes.url}/top/usuarios/">Usuarios</a>
			</div>
		</div>
	</nav>
</div>