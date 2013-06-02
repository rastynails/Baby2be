{* component Cls Item Info *}

{container}

{block title="%bid_title"}
{if $bids_count}
<table class="form">
<tbody>
	<tr>
		<td class="label">{text %total_bids}</td>
		<td class="value"><b>{$bids_count}</b></td>
	</tr>
	<tr>
		<td nowrap="" valign="top" class="label">{text %`$entity`_bid}</td>
		<td valign="top" class="value">{$currency}<b>{$bid_info.bid}</b> {text %by} 
			{profile_username profile_id=$bid_info.profile_id}
		</td>
	</tr>
</tbody>
</table>
{else}
<div class="center">{text %no_bid}</div>	
{/if}
{/block}
{/container}