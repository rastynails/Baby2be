
{canvas}
    {container}

    {if $service_info}
    {capture name='view_all_link'}
        <a href="{document_url doc_key='sms_services'}">{text %view_all}</a>
    {/capture}
    <div class="center_block">
        {block title=%.sms_billing.services.`$service_info.service_key`_title toolbar=$smarty.capture.view_all_link}
            {text %.sms_billing.services.`$service_info.service_key`}
            <br /><br />
            {if $showPrice}{text %cost} {text %.label.currency_sign}{$service_info.cost}<br />{/if}<br />
            <div class="right">{component $sms_PaymentProvider}</div>
        {/block}
    </div>
    {else if $services}
        {foreach from=$services item='service'}
        <div class="float_half_left {cycle values='sms_even,sms_odd'}">
            {block title=%.sms_billing.services.`$service.service_key`_title}
                {text %.sms_billing.services.`$service.service_key`}
                <br /><br />
                {if $showPrice}{text %cost} {text %.label.currency_sign}{$service.cost}<br />{/if}<br />
                <div class="center"><a href="{document_url doc_key='sms_services'}?service={$service.service_key}">{text %order_link}</a></div>
            {/block}
        </div>
        {/foreach}
    {/if}
        
    {/container}
{/canvas}