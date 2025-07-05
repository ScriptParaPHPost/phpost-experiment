<div id="filterByTodos" class="filterBy cleanlist" style="display:block;">
   {foreach from=$tsComments key=i item=c}
      <div class="py-1 px-3">
         <a href="{$c.post_url}#comentarios-abajo" class="block truncate">{$c.post_title}</a>
         <a href="{$c.post_user}" class="font-normal text-sm color-status" data-status="{if $c.user_baneado == 1}baneado{elseif $c.user_activo == 0}activo{elseif $c.post_status > 0}status{$c.post_status}{else}empty{/if}"{if $c.post_status != 0 || $c.user_activo == 0 || $c.user_baneado || $c.c_status != 0} title="{if $c.post_status == 3} El post se encuentra en revisi&oacute;n{elseif $c.post_status == 1} El post se encuentra oculto por acumulaci&oacute;n de denuncias {elseif $c.post_status == 2} El post se encuentra eliminado {elseif $c.c_status == 1} El comentario est&aacute; oculto{elseif $c.user_activo == 0}El autor del comentario tiene la cuenta desactivada{elseif $c.user_baneado == 1}El autor del comentario tiene la cuenta suspendida{/if}"{/if}>@{$c.user_name}</a>
      </div>
   {/foreach}
</div>