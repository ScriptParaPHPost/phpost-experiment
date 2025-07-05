{include "main_header.tpl"}
	
	<div class="flex justify-start items-start gap-4">
		<div class="w-1/2">
			<form method="POST" name="formulario">
				<fieldset>
					<legend class="text-xl font-bold text-center w-full py-2 mb-4">{if $tsAction == 'iniciar'}Iniciar sesión{else}Crear cuenta{/if}</legend>
				
					<label class="block py-3 mb-4 relative">
						<span class="block font-bold mb-1">Nombre de usuario{if $tsAction == 'iniciar'} o email{/if}</span>
						<input type="text" autocomplete="off" name="nickname" placeholder="JohnDoe" class="bg-blue-100 bg-opacity:30 border-blue-300 p-3 block w-full rounded">
						<span class="absolute left-0 text-xs helper" style="margin-bottom: -0.5rem;">Ingresa un nickname</span>
					</label>

					{if $tsAction == 'registro'}
						<label class="block py-3 mb-4 relative">
							<span class="block font-bold mb-1">Email</span>
							<input type="email" autocomplete="off" name="email" placeholder="johndoe@example.com" class="bg-blue-100 bg-opacity:30 border-blue-300 p-3 block w-full rounded">
							<span class="absolute left-0 text-xs helper" style="margin-bottom: -0.5rem;">Tu correo para verificar tu cuenta</span>
						</label>
					{/if}

					<label class="block py-3 mb-4 relative">
						<span class="block font-bold mb-1">Contraseña</span>
						<input type="password" autocomplete="off" name="password" placeholder="123456789" class="bg-blue-100 bg-opacity:30 border-blue-300 p-3 block w-full rounded">
						<kbd class="absolute text-xs bg-blue:200 hover:bg-blue:300 px-2 py-1 rounded cursor-pointer" style="bottom: 1.2rem;right: 1.125rem;" data-show="password" aria-label="Mostrar contraseña">Show</kbd>
						<span class="absolute left-0 text-xs helper" style="margin-bottom: -0.5rem;">La contraseña para acceder a tu cuenta</span>
					</label>

					<label class="py-3 mb-4 relative flex justify-start items-center gap-3">
						<input type="checkbox" name="{if $tsAction == 'registro'}terminos{else}remember{/if}" class="checkbox">
						<span>{if $tsAction == 'registro'}Aceptar los términos y condiciones{else}Recordar mi sesión activa{/if}</span>
					</label>

					<div class="py-4 text-center">
						<input type="submit" class="bg-blue-800 text-blue-50 hover:bg-blue-800 dark:bg-blue-800 dark:hover:bg-blue-900 dark:text-neutral-50 py-2 px-3 rounded" value="{if $tsAction == 'iniciar'}Iniciar sesión{else}Crear cuenta{/if}">
					</div>

				</fieldset>
			</form>
		</div>
		<div class="w-1/2">Lorem, ipsum dolor, sit amet consectetur adipisicing elit. Assumenda est perspiciatis eaque, veniam fugit? Laboriosam, est, corrupti excepturi harum voluptatibus tenetur non autem nesciunt culpa! Enim quam, incidunt cupiditate distinctio?</div>
	</div>

{include "main_footer.tpl"}