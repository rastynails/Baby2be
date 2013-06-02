{* default canvas *}

<div class="top_shad clearfix">
	{component NavigationInventoryLine}
	
	<div class="float_left">
		{component SelectLanguage}
		{component FBCButton}
	</div>
	{ads pos='top'}
</div>
<div id="page_canvas" class="{if $SK.profile}profile_loggedin{else}profile_not_loggedin{/if}">
<div class="page_canvas_r">
	{component SignIn hidden=true}
	<div class="page_canvas_c clearfix">
        {component PageHeader}

        <div class="bg_cont clearfix">
	{if !isset($sidebar) || $sidebar}
		{component PageSidebar}
	{/if}

	<div id="content">
	{ads pos='middle'}
	{component GroupBriefInfo}
	{component BreadCrumb}
    <h1 id="page_content_header">{$content_header}</h1>