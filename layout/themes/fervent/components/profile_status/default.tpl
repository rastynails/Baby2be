{* component Profile Status *}
 
{container stylesheet="profile_status.style"}

{block class="status_bg"}
{block_cap title=%profile_status_header}<div class="profile_status_toggle" {id="profile_status_toggle_cont"}></div>{/block_cap}
<table width="100%">
	<tbody>
		<tr>
			<td width="40%">{text %profile_status_label}:</td>
			<td width="60%"><span {id="status_value"} class="bold"></span></td>
		</tr>
		<tr>
			<td>{text %membership_label}:</td>
			<td>
				<span class="bold">{text %.membership.types.`$membership.membership_type_id`}</span>
				{if $membership.expiration_time}
					({text %expiration_time} {$membership.expiration_time})
				{/if}
				{if $membership.type == 'subscription' && $membership.limit == 'unlimited'}
				    <span class="small">(<a href="{document_url doc_key='payment_selection'}">{text %.membership.membership_selection_title}</a>)</span>
				{/if}
			</td>
		</tr>
        {if $credits_balance}
        <tr>
            <td>{text %credits_left}:</td>
            <td>
                <span class="bold">{$credits_balance}</span>
                <span class="small">(<a href="{document_url doc_key='points_purchase'}">{text %.user_points.buy_more}</a>)</span>
            </td>
        </tr>
		{/if}
		<tr>
			<td colspan="2" class="user_status_update_form">
				<span {id="user_status_curr_value"}
					class="user_status_curr_value">
					{if $user_status}
						{text %profile_status_header}:
						{$username} <a href="javascript://" onclick="$('#status-form').toggle();">{$user_status}</a>
					{/if}
				</span>
				<div class="user_status_form" id="status-form" style="{if $user_status}display: none;{/if}">
				{form UserStatus}
					<div class="float_left">
						<span style="display: none">{label for="status"}</span>
						<span class="username_label">{$username}</span>
					</div>
					<div class="float_left" style="vertical-align:top;">
						{input name="status"}
					</div>
					<div class="float_left" style="vertical-align:top;">{text_formatter for='status' controls='emoticon' entity="profile_status"}</div>
					<div class="float_left" style="vertical-align:top;">{button action="update"}</div>
                    <span class="small status_length">{text %constraint_profile_status_length length=40}</span>
				{/form}
				</div>
			</td>
		</tr>
	</tbody>
</table>
{/block}

{/container}
