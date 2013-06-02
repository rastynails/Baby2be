{* component Events Calendar *}

{container stylesheet="style.style"}
	{capture name='event_month'}
		{$current_month_label}
	{/capture}
    {block title=$smarty.capture.event_month}
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
    <br />
    <div style="float:left;width:48%;"><a href="{$prev_array.url}">&laquo; {$prev_array.label}</a></div><div style="width:48%;float:right;text-align:right;"><a href="{$next_array.url}">{$next_array.label} &raquo;</a></div><div class="clr_div"></div>
    {/block}
{/container}
