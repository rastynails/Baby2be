{* component SubMenu *}
{if $sub_menu_items}
{container stylesheet='navigation_sub_menu.style' class='submenu'}
	{menu type="list" items=$sub_menu_items}	
{/container}
{/if}