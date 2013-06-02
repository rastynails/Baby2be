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
				</tr>				
			</tbody>
		</table>
		<p class="block_submit right">{button action='search'}</p>
	{/form}
{/block}
{/container}
