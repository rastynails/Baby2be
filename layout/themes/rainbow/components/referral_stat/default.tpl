{* Component Referral Statistics *}

{canvas}
	{container stylesheet="referral_stat.style"}
	{if $has_referrals}
		{block class="center_block"}
		{block_cap}{/block_cap}
			{text %for_time}: <select onchange="window.location.href=this.value">
				{foreach from=$months item=item}
					<option value="{$item.url}" {if $item.selected}selected="selected"{/if}>
						{$item.label}
					</option>
				{/foreach}
			</select>
			<br />
			<br />
			<table class="form">
				<thead>
					<tr>
						<th>
							{text %email}
						</th>
						<th>
							{text %date_referred}
						</th>
						<th>
							{text %date_join}
						</th>
					</tr>
				</thead>
				<tbody>
					
					{foreach from=$list item=item name='f'}
						<tr {cycle values='class="list_odd",""'}>
							<td class="label">{$item.email}</td>
							<td class="value">{$item.date_referred|date:"d-m-Y"}</td>
							<td class="value">{$item.date_registered|date:"d-m-Y"}</td>
						</tr>
					{/foreach}
					
				</tbody>
			</table>
			<br />
			{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
		{/block}
	{else}
		{block class="center_block"}
			{capture name="href"}
				{document_url doc_key="invite_friends"}
			{/capture}
			{text %no_items href=$smarty.capture.href}
		{/block}
	{/if}
	{/container}
{/canvas}
