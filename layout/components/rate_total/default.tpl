{* component Rate *}

{container}
<div style="text-align:center;font-size:11px;">
{text %.rate.label_total_rate}: <span class="highlight">{$rate_info.avg_score}</span> ({if !$rate_info.items_count}0{else}{$rate_info.items_count}{/if} {text %.rate.label_rates_count})
    <div style="width:100px;margin:0 auto;">
        <div class="inactive_rate_list">
            <div class="active_rate_list" style="width:{$rate_info.widthp}px;"></div>
         </div>
    </div>
</div>
{/container}
