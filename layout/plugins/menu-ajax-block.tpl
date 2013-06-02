{* navigation menu type:block *}

{include_style file="menu-ajax-block.style"}

<ul class="menu-ajax-block">
{foreach from=$items item='item' name='item'}
	<li class="tab {if $item.active}active{/if} {if $smarty.foreach.item.first}first{/if} {if $smarty.foreach.item.last}last{/if}">
		<a class="{if $item.active}active{/if} {if isset($item.class)}{$item.class}{/if}" rel="{$item.href}" href="javascript://">
			<span>{$item.label|strip}</span>
		</a>
	</li>
{/foreach}
</ul>
