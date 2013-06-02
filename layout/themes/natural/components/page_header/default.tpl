{* component PageHeader *}
	
{container stylesheet="page_header.style" class="page_header"}
{if !empty($is_123wm) && $is_123wm}
	<script> 
		var init_user = "{$u}"; var init_password = "{$p}"; dcInit();
	</script>
{/if}
	{component NavigationInventoryLine}
	<div class="lang_abs">{component SelectLanguage}{component FBCButton}</div>
	<a class="logo" href="{$smarty.const.SITE_URL}"></a>
	{component NavigationMenuMain}
{/container}

