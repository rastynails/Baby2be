{* component BreadCrumb*}
{container class="bread_crumb"}

{foreach from=$bread_crumb key=key item='item' name="crumb"}
	{if $key }&raquo;{/if}
	
	{if $smarty.foreach.crumb.last }
		<b>{$item.label}</b>
	{else}
		<a href="{$item.url}">{$item.label}</a>
	{/if} 
{/foreach}

{/container}