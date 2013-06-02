{container class="keyword_search"}
{block title=%label}	
	{form KeywordSearch}
		<table class="form">
			<tbody>
				<tr>
					<td class="label">{label %.profile_fields.label_search.`$field_id` for=$field}</td>
					<td class="value">{input name=$field}</td>
				</tr>
			</tbody>
		</table>
		<p class="block_submit right">{button action=search}</p>
	{/form}
{/block}
{/container}