{* component Main Search *}

{canvas}
{container stylesheet="main_search.style" class="main_search"}

	{block title=%label}
	{form MainSearch}
		{foreach from=$fields item=section key=sect_id}
			<table class="form">
				<thead>
					<tr>
						<th colspan="2">{text %.profile_fields.section.`$sect_id`}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$section item=id key=name}
						<tr>
							{if $name !='location'}
								<td class="label">{label %.profile_fields.label_search.`$id` for=$name}</td>
								<td class="value">{input name=$name labelsection=profile_fields.value}</td>
							{else}
								<td colspan="2">{input name=$name labelsection=profile_fields.value}</td>
							{/if}
						</tr>	
					{/foreach}
				</tbody>
			</table>
		{/foreach}
		<br />
		<div class="center">
			<table width="100%">
				<tr>
					<td align="right">{input name=search_online_only label=%.profile_fields.label_search.`$sys_fields.search_online_only`}</td>
					<td align="left">{input name=search_with_photo_only label=%.profile_fields.label_search.`$sys_fields.search_with_photo_only`}</td>
				</tr>
			</table>
			<br />
		</div>
		<p align="center">{button action='search'}</p>
	{/form}
	{/block}
	
{/container}

{/canvas}
