
{canvas}

    {container class="points_purchase" stylesheet="points_purchase.style"}
        
        <div class="center_block wider">
        {block title=%.user_points.purchase}
            {form PointsPurchase}
            
                {foreach from=$packages item='p' name='pack'}
                    <div class="package">
                        <label>
                        {if $smarty.foreach.pack.first}
                            {input_item name="package_id" value=$p.package_id checked="checked"}
                        {else}
                            {input_item name="package_id" value=$p.package_id }
                        {/if}  
                        {text %.user_points.packages.package_`$p.package_id`}<br />
                        <span class="package_info highlight">
                            {text %.user_points.package points=$p.points price=`$cur``$p.price`}
                        </span>
                        </label>
                    </div>
                {/foreach}
                
                <div class="providers">
                {if $providers}
                    <span {id="provider_select"}>
                        {text %.components.payment_selection.pay_with}: {input name="provider_id" labelsection='components.payment_selection'}
                    </span>
                    <br />
                    {button action='purchase' class='checkout_btn'}
                    <div class="provider_logos">
                        {foreach from=$providers item='provider'}
                            {if $provider.icon}<img src="{$smarty.const.URL_USERFILES}{$provider.icon}" alt="{text %.components.payment_selection.`$provider.fin_payment_provider_id`}" />{/if}
                        {/foreach}
                    </div>
                {else}
                    {text %.components.payment_selection.no_providers}
                {/if}
                </div>
                
            {/form}
        {/block}
        
        {block title=%.membership.service_cost}
            <table width="100%">
            {foreach from=$services item='serv'}
                <tr class="{cycle values='tr_1,tr_2'}">
                    <td>{text %.membership.services.`$serv.membership_service_key`}</td>
                    <td class="scost"><b>{$serv.credits}</b> <span class="small">{text %.components.profile_status.credits}</span></td>
                </tr>
            {/foreach}
            </table>
        {/block}
        </div>
        
    {/container}

{/canvas}