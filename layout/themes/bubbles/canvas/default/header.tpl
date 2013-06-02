{* default canvas *}

<div id="page_canvas">
		{ads pos='top'}
		{component PageHeader}
		{component BreadCrumb}
	{if !isset($sidebar) || $sidebar}
		{component PageSidebar}
	{/if}
	<div id="content">
		{ads pos='middle'}
		{component GroupBriefInfo}
						
		<div class="block_cap"><div class="block_cap_r"><div class="block_cap_c">
		<h1 id="page_content_header">{$content_header}</h1>
		</div></div></div>
		<div class="block_body"><div class="block_body_r"><div class="block_body_c">
		{component NavigationSubMenu level=1}
