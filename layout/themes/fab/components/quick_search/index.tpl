{* component QuickSearch *}

{container stylesheet="quick_search.style" class="quick_search_index"}
	{block title=%label}
		{form QuickSearch}
			<div class="clearfix">
				<div class="qbody">
					{if $fields.sex}
						<b class="qfield">{label %.profile_fields.label_search.`$fields.sex` for=sex}</b>
						<div class="qfield_right">{input name=sex type='select' labelsection=profile_fields.value class='input_group'}</div>
						<div class="clr_div"></div>
					{/if}
					{if $fields.match_sex}
						<b class="qfield">{label %.profile_fields.label_search.`$fields.match_sex` for=match_sex}</b>
						<div class="qfield_right">{input name=match_sex labelsection=profile_fields.value class=input_group type="select"}</div>
						<div class="clr_div"></div>
					{/if}
					{if $fields.birthdate}
						<b class="qfield q_age">{label %.profile_fields.label_search.`$fields.birthdate` for=birthdate}</b>
						<div class="qfield_right_age">{input name=birthdate}</div>
						<div class="clr_div"></div>
					{/if}
					{if $fields.location}
					<div class="qfield">{input name=location}</div>
                                        {/if}
					<div class="clr_div"></div>
				</div>
				<div>
					<table width="100%">
						<tr>
							<td align="right">{input name=search_online_only label=%.profile_fields.label_search.`$fields.search_online_only`}</td>
							<td align="left">{input name=search_with_photo_only label=%.profile_fields.label_search.`$fields.search_with_photo_only`}</td>
						</tr>
					</table>
				</div>
				<div class="clr_div"></div>
				<div class="qindex_button"><center>{button action='search'}</center></div>
			</div>
		{/form}
	{/block}
{/container}
