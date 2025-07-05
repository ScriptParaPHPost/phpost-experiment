{include "main_header.tpl"}
	
	<div class="grid md:grid-cols-2 sm:grid-cols-1 justify-start items-start gap-4 my-4 w-3/4" style="margin: 0 auto;">
		<div class="left">
			<form method="POST" name="formulario">
				<fieldset>
					<legend class="text-xl font-bold text-center w-full py-2 mb-4">Iniciar sesión</legend>
				
					<label class="block py-3 mb-4 relative">
						<span class="block font-bold mb-1">Nombre de usuario o email</span>
						<input type="text" autocomplete="off" name="nickname" placeholder="JohnDoe" class="bg-blue-100 bg-opacity:30 border-blue-300 p-3 block w-full rounded">
						<span class="absolute left-0 text-xs helper" style="margin-bottom: -0.5rem;"></span>
					</label>

					<label class="block py-3 mb-4 relative">
						<span class="block font-bold mb-1">Contraseña</span>
						<input type="password" autocomplete="off" name="password" placeholder="123456789" class="bg-blue-100 bg-opacity:30 border-blue-300 p-3 block w-full rounded">
						<kbd class="absolute text-xs bg-blue:200 hover:bg-blue:300 px-2 py-1 rounded cursor-pointer" style="bottom: 1.2rem;right: 1.125rem;" data-show="password" aria-label="Mostrar contraseña">Show</kbd>
						<span class="absolute left-0 text-xs helper" style="margin-bottom: -0.5rem;"></span>
					</label>

					<label class="py-3 mb-4 relative flex justify-start items-center gap-3">
						<input type="checkbox" name="remember" class="checkbox">
						<span>Recordar mi sesión activa</span>
					</label>

					<div class="py-4 text-center">
						<input type="submit" name="iniciar" class="bg-blue-800 text-blue-50 hover:bg-blue-800 dark:bg-blue-800 dark:hover:bg-blue-900 dark:text-neutral-50 py-2 px-3 rounded" value="Iniciar sesión">
					</div>

				</fieldset>
			</form>
		</div>
		<div class="h-full rounded">
			<div class="flex justify-center items-center h-full flex-col gap-3 w-full">
				<div class="flex justify-center items-center flex-col w-full gap-3">
					<a href="#" class="bg-google hover:bg-google text-neutral-50 py-2 px-4 w-1/2 text-center rounded">Iniciar sesión con Google</a>
					<a href="#" class="bg-discord hover:bg-discord text-neutral-50 py-2 px-4 w-1/2 text-center rounded">Iniciar sesión con Discord</a>
				</div>
				<div class="text-center md:mt-4 sm:mt-0">
					<span role="button" data-action="account" class="block cursor-pointer mb-4">Activar mi cuenta</span>
					<span role="button" data-action="password" class="block cursor-pointer mb-4">Olvid&eacute; la contraseña</span>
					<p class="block">¿No tienes cuenta en {$tsConfig.titulo}? <a href="{$tsRoutes.url}/registro">Registrate</a></p>
				</div>
			</div>
		</div>
	</div>

{include "main_footer.tpl"}