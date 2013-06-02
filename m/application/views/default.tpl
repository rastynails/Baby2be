<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	{$header}
    <body>
    	<div class="screen">
    		<div class="header">
    			{$home_url}
    			{if isset($username)}<div class="hun">{$username} <a class="small" href="{$logout_url}">({text section='nav_doc_item' key=sign_out})</a></div>{/if}
    		</div>
			{$menu}
			
			{if $messages} 
				{foreach from=$messages item='msg'}
					<div class="notification">{$msg.msg}</div>
				{/foreach}
			{/if}
			
			{if isset($notifications)}
	    	{foreach from=$notifications item='notif'}
	    		<div class="notification"><a href="{$notif.url}">{$notif.title}</a></div>
	    	{/foreach}
	    	{/if}
	    	
	    	<div class="content">
	    		{$content}
	    	</div>
	    	{$footer}
	    </div>
    </body>
</html>