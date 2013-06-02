{* component Chuppo Player *}

{container}
{block}
{block_cap title=%webcam}<a class="delete_cmp" href="javascript://"></a><br clear="all" />{/block_cap}
{if !$no_permission}
<div style="width: 274px; margin: auto;">
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
	 codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" 
	 width="274" height="417" id="recorder" align="middle">

	<param name="allowScriptAccess" value="sameDomain" />
	<param name="movie" value="{$recorder_obj_url}" />
	<param name="quality" value="high" />
	<param name="bgcolor" value="#ffffff" />
	<param name="FlashVars" value="{$query_string}" />
        <param name="wmode" value="opaque" />
	
	<embed src="{$recorder_obj_url}" 
	 	quality="high" 
	 	bgcolor="#ffffff" 
	 	width="274" 
	 	height="417" 
	 	name="recorder" 
	 	align="middle"
        wmode="opaque"
	 	allowScriptAccess="sameDomain" 
	 	type="application/x-shockwave-flash" 
	 	pluginspage="http://www.macromedia.com/go/getflashplayer"
	 	FlashVars="{$query_string}" />
	
</object>
</div>
{else}
	<div class="no_content">{$no_permission}</div>
{/if}
{/block}
{/container}
