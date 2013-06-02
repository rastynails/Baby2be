{canvas}

{container stylesheet="payment_selection.style"}
<br />
{form PaymentSelection provider_id=$providers.0.fin_payment_provider_id plan_id=$default_plan}
<table class="membership_types_tbl" width="100%" {id="membership_types_tbl"}>
	<tr>
		<td class="first_col">
			<div class="current_status">
				{text %membership_label}: <br /> 
				<b>{text %.membership.types.`$membership.membership_type_id`}</b>
				{if $membership.expiration_time}, <br />
				    {text %expiration_time} <b>{$membership.expiration_time}</b>
				    {if $unsub != ''}<div class="unsubscribe">{$unsub}</div>{/if}
                {/if}
                {if $show_balance}
                    <br />
                    {text %.components.profile_status.credits_left}: <br /><b>{$credits_balance}</b>
                    <span class="small">(<a href="{document_url doc_key='points_purchase'}">{text %.user_points.buy_more}</a>)</span>
                {/if}
			</div>
		</td>
		{foreach from=$types item='type'}
		<td style="width: {$col_width}px" class="{if $type.membership_type_id==$status.membership_type_id}current{/if}">
			{block title=%.membership.types.`$type.membership_type_id`}<div class="membership_desc">{text %.membership.types.desc.`$type.membership_type_id`}</div>{/block}
		</td>
		{/foreach}
	</tr>
	<tr>
		<td>
			{if $sms_services}
				<div class="pay_by_sms">{$sms_services}</div>
			{/if}
		</td>
		{foreach from=$type_plans item='type' name='pl'}
			<td style="width: {$col_width}px" class="plans {if $type.membership_type_id==$status.membership_type_id}current{/if}">
			{* {if $type.membership_type_id != $status.membership_type_id} *}
				{foreach from=$type.plans item='plan'}
				<div class="plan_box">
					<label>
					{if $plan.membership_type_plan_id eq $default_plan}
						{input_item name="plan_id" value=`$type.membership_type_id`_`$plan.membership_type_plan_id` checked='checked'}{$plan.label}
					{else}
						{input_item name="plan_id" value=`$type.membership_type_id`_`$plan.membership_type_plan_id`}{$plan.label}
					{/if}
					</label>
				</div>
				{/foreach}
			{* {/if} *}
			</td>
			{assign var="plan_count" value=$smarty.foreach.pl.iteration}
		{/foreach}
	</tr>
	
	{if $coupons_enabled}
	<tr>
        <td colspan="{math equation='x + y' x=$plan_count y=1}" align="right">
            <div style="padding: 10px 0px;">{text %coupon} {input name="coupon"}</div>
        </td>	   
	</tr>
	{/if}
	
	<tr>
		<td colspan="{math equation='x + y' x=$plan_count y=1}">
			<div class="providers">
			{if $providers}
				<span {id="provider_select"} style="">{text %pay_with}: {input name="provider_id" labelsection='components.payment_selection'}</span>
				{button action='checkout' class='checkout_btn'}
				<span {id="claim_btn_label"} class="claim_btn">{text %.forms.payment_selection.actions.claim}</span>
				<div class="provider_logo">
					{foreach from=$providers item='provider'}
						{if $provider.icon}<img src="{$smarty.const.URL_USERFILES}{$provider.icon}" alt="{text %.components.payment_selection.`$provider.fin_payment_provider_id`}" />{/if}
					{/foreach}
				</div>
			{else}
				{$col_count}{text %.components.payment_selection.no_providers}
			{/if}
			</div>			
		</td>
	</tr>
	{if $show_chart}
		<tr align="center" class="mem_diagram_header">
			<td class="first_col">{block_cap title=%.membership.permission_diagram_benefit}{/block_cap}</td>
			{foreach from=$types item='type'}
				<td style="width: {$col_width}px" class="other_cols {if $type.membership_type_id==$status.membership_type_id}current{/if}">
					{block_cap title=%.membership.types.`$type.membership_type_id`}{/block_cap}
				</td>
			{/foreach}
		</tr>
		<tr>
			<td class="first_col">
				{foreach from=$diag.services item='serv'}
					<div class="benefit {cycle values=td_1,td_2}" >{text %.membership.services.`$serv.membership_service_key`}</div>
				{/foreach}
			</td>
			{foreach from=$diag.types item='type' key='k'}
			<td {if $k==$status.membership_type_id}class="current"{/if}>
				{foreach from=$type.services item='type_service'}
					{if $type_service.is_promo eq 'yes'}
						<div class="available no_mark">{text %.components.payment_selection.is_promo}</div>
					{elseif $type_service.service_limit}
						<div class="available no_mark"><span class="amount">{$type_service.service_limit}</span> {text %.components.payment_selection.per_day}</div>
					{elseif $type_service.available eq 'yes'}
						<div class="available">{*text %.components.payment_selection.unlimited*}</div>
					{else}
						<div class="unavailable"></div>
					{/if}
					
				{/foreach}
			</td>
			{/foreach}
		</tr>
	{/if}
</table>
{/form}
<br clear="all" />

{/container}

{/canvas}