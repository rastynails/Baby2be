{* default canvas *}

<div id="page_canvas">
		{include_style file="style.css"}
		{ads pos='top'}
		{component PageHeader}
		{if !isset($sidebar) || $sidebar}
		 {component PageSidebar}
		{/if}
   <div style="float:right;">		
	<div id="content">

		{component NavigationSubMenu level=1}
		{ads pos='middle'}
		{component GroupBriefInfo}
		{component BreadCrumb}
				
		<h1 id="page_content_header">{$content_header}</h1>
