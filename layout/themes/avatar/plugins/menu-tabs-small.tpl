{* navigation menu type:tabs-small *}

{include_style file="menu-tabs-small.style"}

<ul class="menu-tabs-small">
{foreach from=$items item='item'}
	<li class="tab">
		<a {if $item.active}class="active"{/if} href="{$item.href}">
		<span> 
			{$item.label|strip}
		</span>	
		</a>
	</li>
{/foreach}
</ul>