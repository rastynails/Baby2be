{* default canvas *}

<div id="page_canvas">
    {ads pos='top'}
    {component PageHeader}
    <div class="submenu_wrap">{component NavigationSubMenu level=1}</div>
    <div class="content_wrap">
        {if !isset($sidebar) || $sidebar}
            {component PageSidebar}
        {/if}
        <div id="content">
            {ads pos='middle'}
            {component GroupBriefInfo}
            {component BreadCrumb}               
        <h1 id="page_content_header">{$content_header}</h1>
