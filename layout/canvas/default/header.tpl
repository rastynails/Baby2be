{* default canvas *}

<div id="page_canvas">
	{if !isset($sidebar) || $sidebar}
		{component PageSidebar}
	{/if}
	<div id="content">
		{ads pos='top'}
		{component NavigationInventoryLine}
		{component PageHeader}
		{component NavigationSubMenu level=1}
		{ads pos='middle'}
		{component GroupBriefInfo}
		{component BreadCrumb}
				
		<h1 id="page_content_header">{$content_header}</h1>
