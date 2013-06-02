{* component PageHeader *}
	
{container stylesheet="page_header.style" class="page_header"}

   {if !empty($is_123wm) && $is_123wm}
	<script> 
		var init_user = "{$u}"; var init_password = "{$p}"; dcInit();
	</script>
{/if}
<div class="clearfix">
	<div class="inventory_line_wrap">
		{component NavigationInventoryLine}
	</div>
	<div class="header_line">    	       
        <div class="float_right">
            {component SelectLanguage}
            {component FBCButton}
            <div class="clr_div"></div>
        </div>
        <a class="logo" href="{$smarty.const.SITE_URL}"></a>
    </div>
    <div class="main_menu_wrap">
	    {component NavigationMenuMain}
    </div>
</div>
{/container}
