{* default canvas *}

<div class="top clearfix">
	{component FBCButton}
	{component SelectLanguage}
	{component NavigationInventoryLine}
	{ads pos='top'}
</div>
<div id="page_canvas"">
	{component SignIn hidden=true}
        {component PageHeader}

        <div class="bg_cont clearfix">
	{if !isset($sidebar) || $sidebar}
		{component PageSidebar}
	{/if}

	<div id="content">
	{ads pos='middle'}
	{component GroupBriefInfo}
    <h1 id="page_content_header">{$content_header}</h1>
	{component BreadCrumb}
