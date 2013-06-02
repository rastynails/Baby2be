{* component Events Calendar *}

{container stylesheet="style.style"}
	{block}
	{block_cap title=%.components.events_index_calendar.cap_label}
		{if !empty($menu_array)}{menu type="ajax-block" items=$menu_array}{/if}
	{/block_cap}

    <div style="text-align:center;font-weight:bold;padding-bottom:20px;">{$index_label}</div>
	
{if !empty($calendar)}
    	<div {id="calendar_cont"} style="padding: 0px 23px 9px 15px; min-height: 197px;{if !empty($cal_none)}display:none;{/if}">
            <table class="event_calendar" cellspacing="1">
                <tr>
                    <th>{text %.i18n.date.wday_short_00}</th>
                    <th>{text %.i18n.date.wday_short_01}</th>
                    <th>{text %.i18n.date.wday_short_02}</th>
                    <th>{text %.i18n.date.wday_short_03}</th>
                    <th>{text %.i18n.date.wday_short_04}</th>
                    <th>{text %.i18n.date.wday_short_05}</th>
                    <th>{text %.i18n.date.wday_short_06}</th>
                </tr>
                {foreach from=$calendar_array item='week'}
                    <tr>
                        {foreach from=$week item='day'}
                        <td{if $day} class="{if $day.current}current {/if}{if $day.active}active{else}day{/if}{if $day.link} link{/if}"{/if}>{if $day}{if $day.link}<a href="{$day.url}">{$day.date}</a>{else}{$day.date}{/if}{/if}</td>
                        {/foreach}
                    </tr>
                {/foreach}
            </table>
        </div>
  {/if}
{if !empty($list)}   
    
    	<div {id="list_cont"}{if !empty($list_none)} style="display:none;"{/if}>
        	{if $no_events}
            <div class="no_content">{text %.event.no_events}</div>
            {else}
        	<ul class="thumb_text_list">
                {foreach from=$events item='event'}
                <li class="item">
                    <div class="list_thumb"><a href="{$event.event_url}"><img class="thumb" align="left" src="{$event.image_url}" /></a></div>
                    <div class="list_block">
                    	<div class="list_info">
                    		<div class="item_title"><a href="{$event.event_url}">{$event.dto->getTitle()|censor:'event':true}</a></div>
							{text %.event.by} <a href="{$event.profile_url}">{$event.username}</a> , {$event.dto->getStart_date()|spec_date}
                        </div>
                        <div class="list_content">{$event.description|censor:'event'}</div>
                        <div class="list_stat right">{text %.event.location}: <span class="highlight">{$event.country_label}{if $event.state_label}, {$event.state_label}{/if}{if $event.city_label}, {$event.city_label}{/if}</span> | {text %.event.attendees}: <span class="highlight">{if $event.items_count}{$event.items_count}{else}0{/if}</span></div>
                    </div>
                </li>
                {/foreach}
            </ul>
            {/if}
        </div>
{/if}
	{if !$no_events}<div class="index_events_btn"><span><a href="{$events_url}"><span>{text %.event.view_all}</span></a></span></div>{/if}

{/block}
{/container}
