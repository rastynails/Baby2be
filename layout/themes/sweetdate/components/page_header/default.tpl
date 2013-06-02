{* component PageHeader *}
	
{container stylesheet="page_header.style" class="page_header"}

{if !empty($is_123wm) && $is_123wm}
	<script> 
		var init_user = "{$u}"; var init_password = "{$p}"; dcInit();
	</script>
{/if}
	{component NavigationMenuMain}
	<a class="logo" href="{$smarty.const.SITE_URL}"></a>
	{component ProfileStatusLine}
	<br clear="all" style="line-height: 1px; height: 1px;" />
	{component NavigationSubMenu level=1}

{/container}
