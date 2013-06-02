{* default canvas *}
<div class="page-gradient">
<div class="page-wrap">
<div id="page_canvas">
	{component NavigationInventoryLine}
	{component PageHeader}
	<div class="content-wrap clearfix"> 
	<div id="content">
		{ads pos='top'}
		
		{component NavigationSubMenu level=1}
		{ads pos='middle'}
		{component GroupBriefInfo}
		{component BreadCrumb}
				
		<h1 id="page_content_header">{$content_header}</h1>
