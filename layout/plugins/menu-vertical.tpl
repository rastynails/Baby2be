{* navigation menu type:vertical *}

{include_style file="menu-vertical.style"}

<ul class="menu-{$type}">
	{foreach from=$items item='item'}
		<li class="item"><a {if $item.active}class="active"{/if} href="{$item.href}">{$item.label|strip}</a></li>
	{/foreach}
</ul>
