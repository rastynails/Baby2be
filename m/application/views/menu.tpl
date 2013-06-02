
<div class="menubar">
	{foreach from=$main_menu item='m_item' key='k' name='m'}
		<div class="mitem set{$mset} i{$smarty.foreach.m.iteration}{if $m_item.active} active{/if}">
			<a href="{$m_item.link}" class="{$k}">{*$m_item.title*}&nbsp;</a>
		</div>
	{/foreach}
</div>

<h1 class="page_head">{$page_head}</h1>

{if $sub_menu}
<ul class="submenu">
	{foreach from=$sub_menu item='s_item' key='sk'}
		<li><a href="{$s_item.link}" class="{$sk} {if $s_item.active} active{/if}">{$s_item.title}</a></li>
	{/foreach}
	<br clear="all" />
</ul>
{/if}

{if isset($im_inv)} 
	{foreach from=$im_inv item='inv'}
		<div class="notification"><a href="{$inv.link}">{$inv.title}: {$inv.message}</a></div>
	{/foreach}
{/if}