{* component PageHeader *}
	
{container stylesheet="page_header.style" class="page_header clearfix"}

	{if !empty($is_123wm) && $is_123wm}
	<script> 
		var init_user = "{$u}"; var init_password = "{$p}"; dcInit();
	</script>
{/if}
	<div class="float_right">

	{if $topSelectBox}
		<div class="black_bg">
			{component SelectLanguage}
			{component FBCButton}
		</div>
	{/if}
		<div class="clr_div"></div>
		{component NavigationInventoryLine}
	</div>
	<div class="clr_div"></div>
	<a class="logo" href="{$smarty.const.SITE_URL}"></a>
{/container}
