{* navigation menu type:vertical *}

{include_style file="menu-vertical.style"}

<ul class="menu-{$type} clearfix">
	{foreach from=$items item='item'}
		<li class="item {if $item.active}active{/if}"><a class="{if isset($item.class)}{$item.class}{/if}" href="{$item.href}">{$item.label|strip}</a></li>
	{/foreach}
</ul>