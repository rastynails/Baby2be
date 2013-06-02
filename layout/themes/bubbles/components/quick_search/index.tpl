{* component QuickSearch *}

{container stylesheet="quick_search.style" class="quick_search_index"}
	{block title=%label}
		{form QuickSearch}
			{if $fields.sex}
				<b>{label %.profile_fields.label_search.`$fields.sex` for=sex}</b>
				<div class="clearfix">{input name=sex type='radio' labelsection=profile_fields.value class=input_group}</div>
				<hr />
			{/if}
			{if $fields.match_sex}
				<b>{label %.profile_fields.label_search.`$fields.match_sex` for=match_sex}</b>
				<div class="clearfix">{input name=match_sex labelsection=profile_fields.value class=input_group}</div>
				<hr />
			{/if}
			{if $fields.birthdate}
				<b>{label %.profile_fields.label_search.`$fields.birthdate` for=birthdate}</b>
				<div class="center" style="padding: 0px 0px 4px">{input name=birthdate}</div>
				<hr />
			{/if}

                        {if $fields.location}
			<div class="center" style="padding: 1px 0px 4px">{input name=location}</div>
                        {/if}
			<hr />

			<div class="center">
				<table width="100%">
					<tr>
						<td align="right">{input name=search_online_only label=%.profile_fields.label_search.`$fields.search_online_only`}</td>
						<td align="left">{input name=search_with_photo_only label=%.profile_fields.label_search.`$fields.search_with_photo_only`}</td>
					</tr>
				</table>
			</div>

			<div class="center">{button action='search'}</div>
		{/form}
	{/block}
{/container}
