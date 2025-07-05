{include "main_header.tpl"}
{$tsInstall}
	
	<div class="flex gap-4">
		<div class="w-2/3 overflow-hidden" id="izquierda">
			{foreach from=$tsPostsStickys item=p}
				{include "m.home_posts-item.tpl" p=$p}
			{/foreach}
			{foreach from=$tsPosts item=p}
				{include "m.home_posts-item.tpl" p=$p}
			{/foreach}
			{$tsPages}
		</div>
		<div class="w-1/3" id="derecha">
			{include "m.home_stats.tpl"}
			{include "m.home_last_comments.tpl"}
			{include "m.home_top_posts.tpl"}
			{include "m.home_top_users.tpl"}
		</div>
	</div>

{include "main_footer.tpl"}