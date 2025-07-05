{include "main_header.tpl"}

	<div class="grid md:grid-cols-2 sm:grid-cols-1 justify-start items-start gap-4 my-4 lg:w-3/4" style="margin: 0 auto;">
		<div class="left">
			<form method="POST" name="formulario">
				<fieldset>
					<legend class="text-xl font-bold text-center w-full py-2 mb-4">Crear una cuenta</legend>
				
					<label class="block py-3 mb-4 relative">
						<span class="block font-bold mb-1">Nombre de usuario</span>
						<input type="text" autocomplete="off" name="nickname" placeholder="JohnDoe" class="bg-blue-100 bg-opacity:30 border-blue-300 p-3 block w-full rounded">
						<span class="absolute left-0 text-xs helper helper-nickname" style="margin-bottom: -0.5rem;"></span>
					</label>

					<label class="block py-3 mb-4 relative">
						<span class="block font-bold mb-1">Contraseña Deseada</span>
						<input type="password" autocomplete="off" name="password" placeholder="123456789" class="bg-blue-100 bg-opacity:30 border-blue-300 p-3 block w-full rounded mb-4">
						<kbd class="absolute text-xs bg-blue:200 hover:bg-blue:300 px-2 py-1 rounded cursor-pointer" style="top: 3rem;right: 1.125rem;" data-show="password" aria-label="Mostrar contraseña">Show</kbd>
						<span class="absolute left-0 text-xs helper helper-password" style="top: 5.325rem;"></span>
						<div id="strength" class="strength absolute right-0 text-xs" style="top: 4.5rem;"></div>
						<ul id="rules" class="flex flex-wrap">
							<li class="w-full">Al menos debe contener uno de estos</li>
							<li id="symbol" class="invalid flex-grow">🔒 Símbolo</li>
							<li id="uppercase" class="invalid flex-grow">🔡 Mayúscula</li>
							<li id="number" class="invalid flex-grow">🔢 Número</li>
							<li id="length" class="invalid w-1/2">📏 Mínimo 8 caracteres</li>
						</ul>
					</label>

					<label class="block py-3 mb-4 relative">
						<span class="block font-bold mb-1">Correo</span>
						<input type="email" autocomplete="off" name="email" placeholder="johndoe@example.com" class="bg-blue-100 bg-opacity:30 border-blue-300 p-3 block w-full rounded">
						<span class="absolute left-0 text-xs helper helper-email" style="margin-bottom: -0.5rem;"></span>
					</label>

					<div class="block py-3 mb-4 relative">
						<span class="block font-bold mb-1">Género</span>
						<div class="flex justify-start items-center gap-4">
							<label>
								<input type="radio" name="sexo" value="3" checked class="radio">
								<span>No decir</span>
							</label>
							<label>
								<input type="radio" name="sexo" value="0" class="radio">
								<span>Mujer</span>
							</label>
							<label>
								<input type="radio" name="sexo" value="1" class="radio">
								<span>Hombre</span>
							</label>
						</div>
						<span class="absolute left-0 text-xs helper helper-sexo" style="margin-bottom: -0.5rem;"></span>
					</div>

					<label class="py-3 mb-4 relative flex justify-start items-center gap-3">
						<input type="checkbox" name="terminos" class="checkbox">
						<span>Acepto los <a href="{$tsConfig.url}/pages/terminos-y-condiciones/" style="text-decoration: underline;">términos y condiciones</a></span>
						<span class="absolute left-0 text-xs helper helper-terminos" style="margin-bottom: -2.5rem;"></span>
					</label>

					<div class="py-4 text-center">
						<input type="hidden" name="response" id="response" class="g-recaptcha">
						<input type="submit" class="bg-blue-800 text-blue-50 hover:bg-blue-800 dark:bg-blue-800 dark:hover:bg-blue-900 dark:text-neutral-50 py-2 px-3 rounded" value="Crear cuenta">
					</div>

				</fieldset>
			</form>
		</div>
		<div class="h-full bg-neutral-200 rounded">
			<div class="flex justify-center items-center h-full flex-col gap-3">
				<a href="#" class="text-neutral-700 hover:bg-neutra-100 dark:bg-neutral-800 dark:hover:bg-neutral-900 dark:text-neutral-50 py-2 px-4 w-1/2 text-center rounded">Crear con Google</a>
				<a href="#" class="text-neutral-700 hover:bg-neutra-100 dark:bg-neutral-800 dark:hover:bg-neutral-900 dark:text-neutral-50 py-2 px-4 w-1/2 text-center rounded">Crear con Discord</a>

				<div class="text-center mt-4">
					<p class="block">¿Ya tengo cuenta en {$tsConfig.titulo}? <a href="{$tsRoutes.url}/iniciar">Iniciar sesión</a></p>
				</div>
			</div>
		</div>
	</div>

{include "main_footer.tpl"}