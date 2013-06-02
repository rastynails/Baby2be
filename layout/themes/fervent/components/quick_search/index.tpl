{* component QuickSearch *}

{container stylesheet="quick_search.style" class="quick_search"}
	{block title=%label}
		{form QuickSearch}
			{if $fields.sex}
				<b>{label %.profile_fields.label_search.`$fields.sex` for=sex}</b>
				<div class="center clearfix qs_fields_sex">{input name=sex type='radio' labelsection=profile_fields.value class="input_group clearfix"}</div>
			{/if}	
			{if $fields.match_sex}
				<b>{label %.profile_fields.label_search.`$fields.match_sex` for=match_sex}</b>
				<div class="center clearfix qs_fields_match_sex">{input name=match_sex labelsection=profile_fields.value class="input_group clearfix"}</div>
			{/if}
			{if $fields.birthdate}
				<b>{label %.profile_fields.label_search.`$fields.birthdate` for=birthdate}</b>
				<div class="center clearfix qs_fields_birthdate">{input name=birthdate}<br clear="all"></div>
			{/if}			
			<div class="clearfix qs_fields_location">{input name=location}<br clear="all"></div>
			<div class="center clearfix qs_fields_params">
				<table width="100%">
					<tr>
						<td align="right">{input name=search_online_only label=%.profile_fields.label_search.`$fields.search_online_only`}</td>
						<td align="left">{input name=search_with_photo_only label=%.profile_fields.label_search.`$fields.search_with_photo_only`}</td>
					</tr>
				</table>
				<br clear="all">
			</div>            
			<div class="right clearfix">{button action='search' label="Quick Search"}</div>
			<div class="clr_div"></div>
		{/form}
		<div class="clr_div"></div>
	{/block}
{/container}
