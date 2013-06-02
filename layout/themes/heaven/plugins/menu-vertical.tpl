{* navigation menu type:vertical *}

{include_style file="menu-vertical.style"}

<ul class="menu-{$type} clearfix">
	{foreach from=$items item='item'}
		<li class="item {if isset($item.class)}{$item.class}{/if}"><a class="{if $item.active}active{/if}" href="{$item.href}">{$item.label|strip}</a></li>
	{/foreach}
</ul>