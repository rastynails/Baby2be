{* navigation menu type:tabs *}

{include_style file="menu-tabs.style"}
{strip}
<ul class="menu-tabs{if $class} {$class}{/if}">
{foreach from=$items item='item'}
	<li class="tab{if $item.active} active_tab{/if}">
		<a class="{if $item.active}active{/if} {if isset($item.class)}{$item.class}{/if}" href="{$item.href}" {if $empty}title="{$item.label|strip}"{/if}>
			<span>{if $empty}&nbsp;{else}{$item.label|strip}{/if}</span>
		</a>
	</li>
{/foreach}
</ul>
{/strip}