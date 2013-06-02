{*  Theme: Golden Heart   *}
{*  Author: Skadate  *}

{canvas blank}
{include_style file="themes/gh/httpdocs/index/index.style"}
<div id="page_canvas">

	{component NavigationInventoryLine}
	{component PageHeader}
	{component NavigationSubMenu level=1}


	<div id="content" style="padding: 0; width: 830px;">
<div style=" height: 411px; width: 828px; margin-bottom: 10px; background: url({$smarty.const.URL_LAYOUT}themes/gh/img/heart.png) no-repeat center center; ">

        <div class="float_half_left">
                {component QuickSearch}
        </div>
<div style="float: right; width: 244px; color: #f2e0ba; padding: 10px;">Welcome to SkaDate 6 Demo Site! Chat online with other singles, browse and rate profiles with photo and get dates with other members.<br /><br />

Best luck in your search, we hope to send plenty of interesting people your way!</div>
<a href="{document_url doc_key="join_profile"}" style="float: right; display: block; width: 351px; height: 256px;"><img src="{$smarty.const.URL_LAYOUT}themes/gh/img/join.png"></img></a>
</div>

{*<div class="float_half_left" >

{component IndexVideo lists='latest|toprated' active='toprated'}

</div>*}

<div style="float: left; width: 49%; ">{component IndexPhotoList lists="top_rated" count = 6}</div>
<div style="float: right; width: 49%; ">{component IndexProfileList lists="featured" count = 8}</div>
<div style="float: left; width: 49%; ">{component BlogIndexList }</div>
<div style="float: right; width: 49%;">{component IndexVideo lists='latest|toprated' active='toprated'}</div>

</div>
	{component PageSidebar}
        <div id="footer">{component PageFooter}</div>

</div>

{/canvas}