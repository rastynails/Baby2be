{* centered canvas *}


<div class="top_shad clearfix">
	{component NavigationInventoryLine}

	<div class="float_left">
		{component SelectLanguage}
		{component FBCButton}
	</div>
        {ads pos='top'}
</div>

<div id="page_canvas">
<div class="page_canvas_r">
	{include_style file="style.css"}
        {*component NavigationInventoryLine*}
    <div id="content" style="width: 1000px">
   	{component PageHeader}
	{component BreadCrumb}