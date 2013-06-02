<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8"/>
	       {$css_include}
		<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
		{if !isset($js_refresh)}
		<meta http-equiv="refresh" content="10" />
		{else}
		<script language="JavaScript">
		{literal}  
			var timer = null;
			function auto_reload() {
				window.location.reload();
			}
		{/literal}	
		</script>
		{/if}
	</head>
    <body {if isset($js_refresh)} onload="timer = setTimeout('auto_reload()', 10000);"{/if}>
    	<div class="screen">
    	{if $session_expired}
    		<div class="offline">{text section='mobile' key='session_expired'}<br /> {text section='nav_doc_item.headers' key='sign_in'}</div>
    	{elseif $is_online}
	    	{if $messages} 
	    	{foreach from=$messages item='msg'}
	    		<span class="{if $profile_id == $msg.sender_id}my_username{else}opp_username{/if}">{$msg.format_time} {$msg.sender_name}:</span> <span{if isset($msg.color)} style="color:#{$msg.color}"{/if}>{$msg.text}</span><br />
	    	{/foreach}
	    	{/if}
    	{else}
    		<div class="offline">{$offline}</div>
    	{/if}
	    </div>
	    <a name="type" />
    </body>
</html>