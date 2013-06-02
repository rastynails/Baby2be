{* default canvas *}

<div id="page_canvas"> 
		{component SignIn hidden=true}
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
		{ads pos='middle'}
		{component GroupBriefInfo}
		{component BreadCrumb}
        <h1 id="page_content_header">{$content_header}</h1>
	
