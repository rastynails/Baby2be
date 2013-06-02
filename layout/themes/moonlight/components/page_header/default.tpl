{* component PageHeader *}
	
{container stylesheet="page_header.style" class="page_header"}

{if !empty($is_123wm) && $is_123wm}
	<script> 
		var init_user = "{$u}"; var init_password = "{$p}"; dcInit();
	</script>
{/if}

<div class="clearfix" style="height:30px">
	<div class="float_left">{component SelectLanguage}{component FBCButton}</div>
	<div class="float_right">{component NavigationInventoryLine}</div>
</div>
	<a class="logo" href="{$smarty.const.SITE_URL}"></a>
	{component NavigationMenuMain}
{/container}
<div class="clr_div"></div>