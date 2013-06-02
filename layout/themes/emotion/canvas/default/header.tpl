{* default canvas *}
<div class="ads_top_emo">{ads pos='top'}</div>
 
<div class="color_header">
<div id="page_canvas"> 

        	{component SignIn hidden=true}

			{component NavigationInventoryLine}
			{component PageHeader}

    
        <div id="content">
        {component NavigationSubMenu level=1}
		{ads pos='middle'}
		{component GroupBriefInfo}
		{component BreadCrumb}
        <h1 id="page_content_header">{$content_header}</h1>
	
