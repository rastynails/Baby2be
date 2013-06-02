
<div class="footer">
	<ul class="fmenu">
	{foreach from=$menu item='item'}
		<li><a href="{$item.link}" {if $item.active}class="active"{/if}>{$item.title}</a></li>
	{/foreach}
	<br clear="all" />
</ul>

</div>