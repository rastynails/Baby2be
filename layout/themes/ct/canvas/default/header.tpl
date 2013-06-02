{* default canvas *}

<div id="page_canvas"> 
		{component SignIn hidden=true}
   		{ads pos='top'}
		{component NavigationInventoryLine}
		{component PageHeader}
   
        <div class="content_wrap">
	<div id="content">
  		{component NavigationSubMenu level=1}
		{ads pos='middle'}
		{component GroupBriefInfo}
		{component BreadCrumb}
        <h1 id="page_content_header">{$content_header}</h1>
	
