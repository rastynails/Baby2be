{* navigation menu type:block *}

{include_style file="menu-block.style"}

<ul class="menu-block">
{foreach from=$items item='item'}
	<li class="tab {if $item.active}active{/if}">
		<a class="{if $item.active}active{/if} {if isset($item.class)}{$item.class}{/if}" href="{$item.href}">
			<b><span>{$item.label|strip}</span></b>
		</a>
	</li>
{/foreach}
</ul>
