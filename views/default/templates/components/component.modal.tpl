<template id="MyModal">
	<div class="fixed z-90 w-screen h-screen bg-neutral:900 dark:bg-neutral:300 bg-opacity:10 blur-md flex justify-center items-center">
		<div class="modal w-1/3 bg-neutral:50 dark:bg-neutral:950 dark:text-neutral:100 rounded shadow">
			<div class="flex justify-between items-center p-3">
				<div class="font-bold text-lg" id="modalTitle"></div>
				<span class="block text-md cursor-pointer" id="modalClose">&times;</span>
			</div>
			<div class="py-3 px-6 min-h-50dvh max-h-25dvh overflow-y-auto">
				<div class="text-center py-6" id="modalLoading">
					<img src="{$tsRoutes.images}/loading_bar.gif" alt="cargando sistemas">
					<span class="font-medium text-lg uppercase block text-center">Cargando...</span>
				</div>
				<div id="modalContent"></div>
			</div>
			<div class="flex justify-center items-center gap-4 py-4" id="modalFooter">
				<button type="text" class="bg-neutral:800 hover:bg-neutral:900 outline-neutral:800 text-neutral:50 py-2 px-4 rounded text-cetenter cursor-pointer" id="buttonContinue">Continuar</button>
				<button type="text" class="bg-neutral:50 hover:bg-neutral:100 outline-neutral:800 text-neutral:950 py-2 px-4 rounded text-cetenter cursor-pointer" id="buttonCancel">Continuar</button>
			</div>
		</div>
	</div>
</template>