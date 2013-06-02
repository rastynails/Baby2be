{* Component Join*}

{canvas}

{container stylesheet="join.style" class="center_block"}
{form Join}
<div class="steps">{text %.profile_fields.page_join.`$step`}</div>
	{block title=%label}
		
		
			{foreach from=$fields item=section key=sect_id}
				<table class="form">
					<thead>
						<tr>
							<th colspan="2">{text %.profile_fields.section.`$sect_id`}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$section item=field}
							<tr>
								{if $field.presentation =='location'}
									<td colspan="2">
										<div style="display: none">
											{label for=$field.name}
										</div>
										{input name=$field.name labelsection=profile_fields.label_join}
									</td>
								{else}
									<td class="label">
									   {label %.profile_fields.label_join.`$field.id` for=$field.name}{if $field.required}<span class="required_star">*</span>{/if}
									</td>
									<td class="value">{input name=$field.name labelsection=profile_fields.value}</td>
								{/if}
								
							</tr>
							{if $field.confirm}
								<tr>
									<td class="label">
									   {label %.profile_fields.confirm.`$field.id` for="re_`$field.name`"}{if $field.required}<span class="required_star">*</span>{/if}
									</td>
									<td class="value">{input name="re_`$field.name`" labelsection=profile_fields.value}</td>								
								</tr>
							{/if}
							
								
						{/foreach}
					</tbody>
				</table>
					
			{/foreach}
			{if $has_captcha}
				<br clear="all">
				<table class="form">
					<thead>
						<tr>
							<th colspan="2">{label %captcha_label for="captcha"}</th>
						</tr>
					</thead>
					<tbody>
					<tr><td colspan="2" class="captcha">{input name=captcha}</td></tr>
					</tbody>
				</table>
				<br clear="all">	
			{/if}
			<p align="center" class="join_btn">{button action=$form->active_action label=%.forms._actions.join}</p>	
		
	{/block}
{/form}
{/container}

{/canvas}