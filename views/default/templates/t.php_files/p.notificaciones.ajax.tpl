{foreach from=$tsData item=noti}
   <div class="flex flex-row-reverse justify-start items-center gap-3 bg-blue-100 p-2 rounded{if $noti.unread > 0} bg-blue-200{/if} hover:bg-blue-200 hover:bg-opacity:30 cursor-pointer" data-type="{$noti.style}">
      <img src="{$noti.avatar}" alt="avatar foto perfil usuario" class="placeholder square-md object-cover rounded aspect-square">
      <div class="lh-tight flex-grow">
         {if $noti.total == 1}
            <a href="{$tsConfig.url}/@{$noti.user}" title="{$noti.user}" class="font-bold">{$noti.user}</a>
         {/if}
         <span class="block text-sm">{$noti.text} <a title="{$noti.ltit}" class="font-bold" href="{$noti.link}">{$noti.ltext}</a></span>
         <time class="text-xs">Hace 10 horas</time>
      </div>
   </div>
{foreachelse}
   <div class="flex justify-center items-center p-2 rounded">
      <span>No hay notificaciones</span>
   </div>
{/foreach}