{* navigation menu type:list *}

{include_style file="menu-list.style"}

<ul class="menu-{$type}">
{foreach from=$items item='item'}
	<li class="item"><a {if $item.active}class="active"{/if} href="{$item.href}"><span>{$item.label|strip}</span></a></li>
	{if  $item.active && $item.sub_menu}
		<br><br>
		{include file=$tpl_self items=$item.sub_menu}
	{/if}
{/foreach}
</ul>
