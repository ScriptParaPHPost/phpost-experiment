{foreach from=$tsMensajes.data item=mp}
	<a class="flex flex-row-reverse justify-start items-center gap-3 bg-blue-100 p-2 rounded{if $noti.unread > 0} bg-blue-200{/if} hover:bg-blue-200 hover:bg-opacity:30 cursor-pointer" href="{$tsConfig.url}/mensajes/leer/{$mp.mp_id}" title="{$mp.mp_subject}">
		<img src="{$tsUser->setAvatar($mp.mp_from)}" alt="avatar foto perfil usuario {$mp.user_name}" class="placeholder square-md object-cover rounded aspect-square">
		<div class="lh-tight flex-grow min-w-0">
			<div class="subject text-md font-bold truncate w-full">{$mp.mp_subject}</div>
			<span class="block text-sm truncate w-full">{$mp.mp_preview}</span>
			<div class="text-xs flex justify-between items-center">
				<small class="font-semibold">{$mp.user_name}</small>
				<time>{$mp.mp_date|fecha}</time>
			</div>
		</div>
	</a>
{foreachelse}
   <div class="flex justify-center items-center p-2 rounded">
      <span>No tienes mensajes</span>
   </div>
{/foreach}