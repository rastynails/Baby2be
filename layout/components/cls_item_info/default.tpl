{* component Cls Item Info *}

{container}

{block title=%item_info_title}
<table class="form">
		<tbody><tr>
			<td class="label">{text %posted}</td>
			<td class="value">
				{text %by} {profile_username profile_id=$item_info.profile_id}, {$item_info.create_stamp|spec_date}
            </td>                
		</tr>
		{if $item_info.end_stamp}
		<tr>
			<td nowrap="" class="label">{text %item_ends}</td>
			<td class="value">{if $item_info.item_ended}<b>{text %item_ended}</b>{else}{$item_info.end_stamp|spec_date}{/if}</td>
		</tr>
		{/if}
		<tr>
			<td class="label">{text %.components.cls.`$item_info.entity`_money}</td>
			<td class="value">
				{item_budget_price entity=$item_info.entity budget_min=$item_info.budget_min budget_max=$item_info.budget_max price=$item_info.price currency=$item_info.currency}
			</td>
		</tr>		
		{if $item_info.edited_by_profile_id}
		<tr>
			<td class="label">{text %edited}</td>
			<td class="value">
				{text %by} {profile_username profile_id=$item_info.edited_by_profile_id}, {$item_info.edit_stamp|spec_date}			
			</td>
		</tr>		
		{/if}
	</tbody>
</table>
{if $allow_payment && $item_info.payment_dtls != ''}<div class="center" style="padding: 10px;">{$item_info.payment_dtls}</div>{/if}
{/block}

{/container}