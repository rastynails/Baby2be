{* component QuickSearch *}

{container stylesheet="quick_search.style" class="quick_search"}
	{block title=%label}
		{form QuickSearch}
			{if $fields.sex}
				<b>{label %.profile_fields.label_search.`$fields.sex` for=sex}</b>
				<div class="center clearfix">{input name=sex type='radio' labelsection=profile_fields.value class="input_group clearfix"}</div>
			{/if}
			{if $fields.match_sex}
				<b>{label %.profile_fields.label_search.`$fields.match_sex` for=match_sex}</b>
				<div class="center clearfix">{input name=match_sex labelsection=profile_fields.value class="input_group clearfix"}</div>
			{/if}
			{if $fields.birthdate}
				<b>{label %.profile_fields.label_search.`$fields.birthdate` for=birthdate}</b>
				<div class="center clearfix">{input name=birthdate}</div>
			{/if}
			<br />
                        {if $fields.location}
			<div class="clearfix">{input name=location}</div>
			<br />
                        {/if}
			<div class="center clearfix">
				<table width="100%">
					<tr>
						<td align="right">{input name=search_online_only label=%.profile_fields.label_search.`$fields.search_online_only`}</td>
						<td align="left">{input name=search_with_photo_only label=%.profile_fields.label_search.`$fields.search_with_photo_only`}</td>
					</tr>
				</table>
				<br />
			</div>
			<div class="center clearfix">{button action='search' label=""}</div>

			<div class="clr_div"></div>
		{/form}
		<div class="clr_div"></div>
	{/block}
{/container}
