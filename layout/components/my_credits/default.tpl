
{canvas}
	{container stylesheet="my_credits.style"}
	
	<div class="center_block" style="width: 80%">
	
    {text %.components.profile_status.credits_left}:
    <span class="bold">{if $credits_balance == 0}0{else}{$credits_balance}{/if}</span>
    <span class="small">(<a href="{document_url doc_key='points_purchase'}">{text %.user_points.buy_more}</a>)</span>
    <br /><br />
	
	{block title=%credits_history}
        <table width="100%">
        {foreach from=$log item='e'}
        <tr class="{cycle values=tr_1,tr_2}">
	       <td class="{if $e.sign == '+'}plus{else}minus{/if}">&nbsp;</td>
	       <td class="credits">{$e.amount}</td>
	       <td>
            {if $e.service}
                {capture assign='service'}{text %.membership.services.`$e.action`}{/capture}
                {text %credits_taken service=$service}
            {elseif $e.action == 'given_by_admin'}
                {text %credits_given_by_admin}
            {else}
                {capture assign='action'}{text %.user_points.actions.`$e.action`}{/capture}
                {text %credits_given action=$action}
            {/if}
           </td>
           <td class="small right">{$e.log_timestamp|spec_date}</td>
	    </tr>   
        {/foreach}
        </table>
    {/block}
    {paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
    </div>
    <br /><br />
	{/container}
{/canvas}