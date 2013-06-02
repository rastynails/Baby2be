{* navigation menu type:line *}
{include_style file="menu-line-bottom.style"}

<ul class="menu-{$type}">
{foreach from=$items item='item' name='menu'}
	<li>
		<a href="{$item.href}" {if $item.active}class="active"{/if}>{$item.label|strip}</a> {if !$smarty.foreach.menu.last}&#183;{/if}
	</li>
{/foreach}
</ul>
