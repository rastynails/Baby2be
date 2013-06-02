{* component Rate *}

{container stylesheet="style.style"}
{capture name='html_code'}
<div style="float:left;width:49%;text-align:center;font-size:11px;">
    {text %.rate.label_your_rate}: <span {id="user_rate"} class="highlight">{$rate_array.score}</span>
    
    <div class="rates_cont">
        {foreach from=$rate_array.items item='rate'}
            <a href="javascript://" class="rate_item" {id="`$rate.id`"}>&nbsp;</a>
        {/foreach}<br clear="all" />
    </div>
</div>
<div style="float:right;width:49%;">
    <div {id="overall_score_cont"}>{component $total_rate}</div>
</div>
{if $no_rate}<div style="display:none;" {id="no_rate"}>{$no_rate}</div>{/if}
<div style="clear:both"></div>
{/capture}
	{if $block}
	{block title=%.rate.label_rate_cap}
    	{$smarty.capture.html_code}
	{/block}
    {else}
    	{$smarty.capture.html_code}
    {/if}
{/container}
