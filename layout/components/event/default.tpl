{* Event httpdoc template *}

{canvas}
	{if $err_message}
    	<div class="no_content">{$err_message}</div>
    {else}
	{if $edit_url}
    <div style="text-align:right;padding:5px 0;">
    	<input type="button" value="{text %.components.event.event_edit}" onclick="window.location='{$edit_url}'" />&nbsp;
        {if $approve_button}<input type="button" {id="approve"} value="{text %.blogs.label_approve}" /> {/if}
        {if $block_button}<input type="button" {id="approve"} value="{text %.label.moder_block}" /> {/if}
        {if $delete_button}<input type="button" {id="delete"} value="{text %.label.moder_delete}" /> {/if}
    </div>
	{/if}
	<div style="float:left;width:59%;">
    	{block_cap title=$event.title|censor:'event':true}{/block_cap}
        {block}{$event.desc|censor:'event'}{/block}
        {component $event_comments}
    </div>
    <div style="float:right;width:40%;">
    	{if $image_url}<img src="{$image_url}" /><br /><br />{/if}
    	{block_cap title=%.event.event_info}{/block_cap}
        {block}
        	<table class="form">
                <tbody>
                	{if $event.country_label}
                    <tr>
                        <td class="label">{text %.event.location}:</td>
                        <td class="value">{$event.country_label}{if $event.state_label}, {$event.state_label}{/if}{if $event.city_label}, {$event.city_label}{/if}{if $event.dto->getCustom_location()}, {$event.dto->getCustom_location()}{/if}</td>
                    </tr>
                    {/if}
                    {if $event.dto->getAddress()}
                    <tr>
                        <td class="label">{text %.event.address}:</td>
                        <td class="value">{$event.dto->getAddress()}</td>
                    </tr>
                    {/if}
                    <tr>
                        <td class="label">{text %.event.date_time}</td>
                        <td class="value">{$event.dto->getStart_date()|spec_date:false:true}</td>
                    </tr>
                    <tr>
                    	<td class="label">{text %.event.end_date}</td>
                        <td class="value">{$event.dto->getEnd_date()|spec_date:false:true}</td>
                    </tr>
                    {if $bookmarks_url}
                    <tr>
                        <td colspan="2" class="label"><a href="{$bookmarks_url}">{text %.nav_menu_item.bookmarks}</a></td>
                    </tr>
                    {/if}
                </tbody>
            </table>
        {/block}
        {if $not_expired}
            {if $event_attend}
                {component $event_attend}
            {else}                
                {block}
                    {$speed_dating_err_message}
                {/block}
            {/if}
        {/if}
            {component $event_attendees}
            {component $event_not_attendees}
    </div>
   {/if}
{/canvas}
