{* component Events List *}

{container}
	{if $speed_dating_events}
	{capture name='speed_dating_events_list'}
		{$speed_dating_cap_label}
	{/capture}
	    {block title=$smarty.capture.speed_dating_events_list}
	        <ul class="thumb_text_list">
	    	{foreach from=$speed_dating_events item='event'}
			<li class="item">
	            <div class="list_thumb">
	            <a href="{$event.event_url}">
	            <img class="thumb" align="left" src="{$event.image_url}" />
	            </a></div>
	            <div class="list_block">
	            	<div class="list_info">
	            		<div class="item_title"><a href="{$event.event_url}">{$event.dto->getTitle()|censor:'event':true}</a></div>
	            		{text %.event.by} <a href="{$event.profile_url}">{$event.username}</a> , {$event.dto->getStart_date()|spec_date}
	            	</div>
	                <div class="list_content">{$event.description|censor:'event'|smile|out_format}</div>
	                <div class="list_stat right">{text %.event.attendees}: <span class="highlight">{if $event.items_count}{$event.items_count}{else}0{/if}</span></div>
	            </div>
	        </li>
	        {/foreach}
	        </ul>	        
	    {/block}
    {/if}
    	{capture name='events_list'}
		{$cap_label}
	{/capture}
    {block title=$smarty.capture.events_list}
    	{if $no_events}<div class="no_content">{text %.event.no_events}</div>{else}
        <ul class="thumb_text_list">
    	{foreach from=$events item='event'}
		<li class="item">
            <div class="list_thumb">
            <a href="{$event.event_url}">
            <img class="thumb" align="left" src="{$event.image_url}" />
            </a></div>
            <div class="list_block">
            	<div class="list_info">
            		<div class="item_title"><a href="{$event.event_url}">{$event.dto->getTitle()|censor:'event':true}</a></div>
            		{text %.event.by} <a href="{$event.profile_url}">{$event.username}</a> , {$event.dto->getStart_date()|spec_date}
            	</div>
                <div class="list_content">{$event.description|censor:'event'|smile|out_format}</div>
                <div class="list_stat right">{text %.event.location}: <span class="highlight">{$event.country_label}{if $event.state_label}, {$event.state_label}{/if}{if $event.city_label}, {$event.city_label}{/if}</span> | {text %.event.attendees}: <span class="highlight">{if $event.items_count}{$event.items_count}{else}0{/if}</span></div>
            </div>
        </li>
        {/foreach}
        </ul>
        {/if}
    {/block}

    {if !empty($pevents)}
        {block title=$pevents_title}
            <ul class="thumb_text_list">
            {foreach from=$pevents item='event'}
            <li class="item">
                <div class="list_thumb">
                <a href="{$event.event_url}">
                <img class="thumb" align="left" src="{$event.image_url}" />
                </a></div>
                <div class="list_block">
                    <div class="list_info">
                        <div class="item_title"><a href="{$event.event_url}">{$event.dto->getTitle()|censor:'event':true}</a></div>
                        {text %.event.by} <a href="{$event.profile_url}">{$event.username}</a> , {$event.dto->getStart_date()|spec_date}
                    </div>
                    <div class="list_content">{$event.description|censor:'event'|smile|out_format}</div>
                    <div class="list_stat right">{text %.event.location}: <span class="highlight">{$event.country_label}{if $event.state_label}, {$event.state_label}{/if}{if $event.city_label}, {$event.city_label}{/if}</span> | {text %.event.attendees}: <span class="highlight">{if $event.items_count}{$event.items_count}{else}0{/if}</span></div>
                </div>
            </li>
            {/foreach}
            </ul>
        {/block}
    {/if}

{/container}
