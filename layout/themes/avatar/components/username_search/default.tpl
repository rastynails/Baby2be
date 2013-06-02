{* Component Username Search *}

{container class='username_search'}
{capture name=label}
{text %label field=%.profile_fields.label.`$field.id`}
{/capture}
{block title=$smarty.capture.label}
	{form UsernameSearch}
		<table class="form">
			<tbody>
				<tr>
					<td class="label">{label %.profile_fields.label.`$field.id` for=$field.name}</td>
					<td class="value">{input name=$field.name}</td>
                    <td class="td_center"><p class="right">{button action='search'}</p></td>
				</tr>				
			</tbody>
		</table>
	
	{/form}
{/block}
{/container}
