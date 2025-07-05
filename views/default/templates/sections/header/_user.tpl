<div class="flex justify-end items-center gap-3">
	{if $tsUser->is_member}
		<div class="notificaciones relative">
			<span role="button" aria-label="Notificaciones del usuario" class="hover:bg-blue-100 dark:hover:bg-blue-900 flex justify-start items-start gap-2 py-2 px-3 rounded" onclick="notificationes.last(); return false" title="Notificaciones del usuario" name="Notificaciones" data-popup="0">
				<svg-icon name="bell" class="square-xs"></svg-icon>
			</span>
			<div class="absolute mt-3 bg-neutral-100 dark:bg-neutral-900 rounded shadow p-2 right-0 dropoff z-10" id="ListNotificaciones" style="width: 330px;">
				{include "component.list-header.tpl" link="/notificaciones/" label="Notificaciones" sublink="/notificaciones/settings/" title="Configurar notificaciones" icon="cog"}
				{include "component.list-nots-mps.tpl"}
			</div>
		</div>
		<div class="mensajes relative">
			<a href="{$tsRoutes.url}/mensajes/" role="button" aria-label="Mensajes del usuario" class="hover:bg-blue-100 dark:hover:bg-blue-900 flex justify-start items-start gap-2 py-2 px-3 rounded" onclick="mensaje.last(); return false" title="Mensajes del usuario" name="Mensajes" data-popup="0">
				<svg-icon name="email" class="square-xs"></svg-icon>
			</a>
			<div class="absolute mt-3 bg-neutral-100 dark:bg-neutral-900 rounded shadow p-2 right-0 dropoff z-10" id="ListMensajes" style="width: 330px;">
				{include "component.list-header.tpl" link="/mensajes/" label="Mensajes" sublink="/mensajes/leer/" title="Leer mensajes" icon="filter"}
				{include "component.list-nots-mps.tpl"}
			</div>
		</div>
		<div class="usuario relative">
			<span role="button" aria-label="Mi perfil {$tsUser->nick}" class="hover:bg-blue-100 dark:hover:bg-blue-900 flex flex-row-reverse justify-start items-start gap-2 py-2 px-3 rounded" onclick="usuario.last()" title="Mi perfil" name="Usuario" data-popup="{$tsAvisos}">
				<img src="{$tsUser->avatar}" loading="lazy" alt="Mi perfil {$tsUser->nick}" class="square-xs aspect-square object-cover rounded-full pointer-events-none">
				<span class="font-bold pointer-events-none">{$tsUser->nick}</span>
			</span>
			<div class="absolute mt-3 bg-neutral-100 dark:bg-neutral-900 rounded shadow p-2 right-0 dropoff z-10" id="ListUsuario" style="width: 330px;">
				<div class="flex flex-col gap-3">
					{if $tsUser->is_member && $tsUser->is_admod}
					<a class="flex justify-start items-center gap-3 hover:bg-blue-100 dark:hover:bg-blue-900 py-2 px-3 rounded" title="Administración" href="{$tsRoutes.url}/admin/">
						<svg-icon name="gem" class="square-xs"></svg-icon>
						<span class="flex justify-between items-center flex-grow">Administración</span>
					</a>
					<hr>
					{/if}
					<a class="flex justify-start items-center gap-3 hover:bg-blue-100 dark:hover:bg-blue-900 py-2 px-3 rounded" title="Mi cuenta" href="{$tsRoutes.url}/cuenta/">
						<svg-icon name="management" class="square-xs"></svg-icon>
						<span class="flex justify-between items-center flex-grow">Mi cuenta</span>
					</a>
					{if $tsAvisos}
					<a class="flex justify-start items-center gap-3 hover:bg-blue-100 dark:hover:bg-blue-900 py-2 px-3 rounded" title="Avisos" href="{$tsRoutes.url}/mensajes/avisos/">
						<svg-icon name="bullhorn" class="square-xs"></svg-icon>
						<span class="flex justify-between items-center flex-grow">Alertas <span class="font-bold px-3 block bg-blue-100">{$tsAvisos}</span></span>
					</a>
					{/if}
					<a class="flex justify-start items-center gap-3 hover:bg-blue-100 dark:hover:bg-blue-900 py-2 px-3 rounded" title="Mis Favoritos" href="{$tsRoutes.url}/favoritos.php">
						<svg-icon name="star" class="square-xs"></svg-icon>
						<span class="flex justify-between items-center flex-grow">Mis Favoritos</span>
					</a>
					<a class="flex justify-start items-center gap-3 hover:bg-blue-100 dark:hover:bg-blue-900 py-2 px-3 rounded" title="Mis Borradores" href="{$tsRoutes.url}/borradores.php">
						<svg-icon name="trash" class="square-xs"></svg-icon>
						<span class="flex justify-between items-center flex-grow">Mis Borradores</span>
					</a>
					<hr>
					<a class="flex justify-start items-center gap-3 hover:bg-blue-100 dark:hover:bg-blue-900 py-2 px-3 rounded" title="Cerrar sesión" href="{$tsRoutes.url}/login-salir.php">
						<svg-icon name="power-off" class="square-xs"></svg-icon>
						<span class="flex justify-between items-center flex-grow">Cerrar sesión</span>
					</a>
				</div>
			</div>
		</div>
	{else}
		<a href="{$tsRoutes.url}/iniciar" role="button" aria-label="Iniciar sesión en mi cuenta" class="bg-blue-800 text-blue-50 hover:bg-blue-800 dark:bg-blue-800 dark:hover:bg-blue-900 dark:text-neutral-50 flex justify-start items-start gap-2 py-2 px-3 rounded">
			<svg-icon name="logIn" class="square-xs"></svg-icon>
			<span class="font-semibold">Iniciar Sesión</span>
		</a>
		<a href="{$tsRoutes.url}/registro" role="button" aria-label="Crear mi cuenta gratis" class="hover:bg-blue-100 dark:hover:bg-blue-900 flex justify-start items-start gap-2 py-2 px-3 rounded">
			<svg-icon name="flash" class="square-xs"></svg-icon>
			<span class="font-semibold">Crear cuenta</span>
		</a>
	{/if}
</div>