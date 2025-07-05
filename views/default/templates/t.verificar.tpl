{include "main_header.tpl"}
	
	<div class="border-blue-100 rounded shadow-sm p-4 my-8 w-2/3 mx-auto">
		<div class="text-center py-3">
			<h2 class="font-bold text-2xl">{$tsAviso.titulo}</h2>
		</div>
		{if $tsAviso.mensaje}
			<p class="text-center my-4 font-semibold">{$tsAviso.mensaje}</p>
		{/if}
		{if $tsAviso.botonLink || $tsAviso.botonTexto}
			{if $tsAviso.botonLink === 'submit'}
				<form method="post">
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

					<div class="py-4 text-center">
						<input type="submit" class="bg-blue-800 text-blue-50 hover:bg-blue-800 dark:bg-blue-800 dark:hover:bg-blue-900 dark:text-neutral-50 py-2 px-3 rounded" value="{$tsAviso.botonTexto}">
					</div>
				</form>
			{else}
				<div class="py-4 text-center">
					<a href="{$tsAviso.botonLink}" class="bg-blue-800 text-blue-50 hover:bg-blue-800 dark:bg-blue-800 dark:hover:bg-blue-900 dark:text-neutral-50 py-2 px-3 rounded">{$tsAviso.botonTexto}</a>
				</div>
			{/if}
		{/if}
	</div>

{include "main_footer.tpl"}