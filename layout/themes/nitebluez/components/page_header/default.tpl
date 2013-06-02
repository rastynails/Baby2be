{* component PageHeader *}
	
{container stylesheet="page_header.style" class="page_header clearfix"}

	{if !empty($is_123wm) && $is_123wm}
		<script> 
			var init_user = "{$u}"; var init_password = "{$p}"; dcInit();
		</script>

	{/if}
	<div class="clearfix">
		<div class="float_right">{component NavigationInventoryLine}</div>
		<div class="float_left">
			{component SelectLanguage}
			{component FBCButton}	       
		</div>
	</div>
	<div class="clr_div"></div>
	{component NavigationMenuMain}
	<a class="logo" href="{$smarty.const.SITE_URL}"></a>
	{component NavigationSubMenu level=1}

{/container}
<div class="clr_div"></div>