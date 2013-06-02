{container class="keyword_search"}
{block title=%label}	
	{form KeywordSearch}
		<table class="form">
			<tbody>
				<tr>
					<td class="label">{label %.profile_fields.label_search.`$field_id` for=$field}</td>
					<td class="value">{input name=$field}</td>
                    <td class="td_center"><p align="right">{button action=search}</p></td>
				</tr>
			</tbody>
		</table>
	{/form}
{/block}
{/container}
