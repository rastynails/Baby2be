{* Events httpdoc template *}

{canvas}
    <div style="float:left;width:57%;">
    	{component $event_list}
    </div>
    <div style="float:right;width:42%;">
    	{component $calendar}
        {component EventAdd}
        
        {if $eventSpeedDatingAdd}
        	{component $eventSpeedDatingAdd}
        {/if} 
    </div>
   <br clear="all" />
{/canvas}
