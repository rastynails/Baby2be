{* centered canvas *}

<div id="page_canvas">
	{include_style file="style.css"}
        {ads pos='top'}
	<div class="clearfix">
			<div class="float_right">
				{component SelectLanguage}
				{component FBCButton}        
			</div>
			<div class="clr_div"></div>
			{component NavigationInventoryLine}
	</div>
	{component PageHeader}
	{component NavigationSubMenu level=1}
    <div id="content">
	
	{component BreadCrumb}