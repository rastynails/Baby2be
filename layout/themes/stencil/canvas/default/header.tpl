{* default canvas *}

<div id="page_canvas">
	{ads pos='top'}
	{component PageHeader}
	{component NavigationMenuMain}
	<div id="content">
		<div class="content_top">
		<div class="content_bot clearfix">
			{component NavigationSubMenu level=1}
			<div class="clr_div"></div>
			{component BreadCrumb}
			<h1 id="page_content_header">{$content_header}</h1>
			{if !isset($sidebar) || $sidebar}
				{component PageSidebar}
			{/if}
			<div class="content_float_right">
			{ads pos='middle'}
			{component GroupBriefInfo}

					
