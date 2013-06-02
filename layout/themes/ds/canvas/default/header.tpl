{* default canvas *}

<div id="page_canvas">
    	{ads pos='top'}
{if !isset($sidebar) || $sidebar}
		{component PageSidebar}
	{/if}
	{component SignIn hidden=true}	

	{component NavigationInventoryLine}
	<div id="content">
    {component PageHeader}
   	{component NavigationSubMenu level=1}
	{ads pos='middle'}
	{component GroupBriefInfo}
	{component BreadCrumb}
    <h1 id="page_content_header">{$content_header}</h1>