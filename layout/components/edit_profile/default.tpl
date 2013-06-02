{*Component Profile Edit*}

{canvas}
	{container stylesheet="edit_profile.style" class="center_block"}
		<div class="change_pass_cont">
			{component ChangePassword}
			<br clear="all">
		</div>

		{if $fbcButton}
            <div class="fbc_synch_c">
                {component $fbcButton}
            </div>
		{/if}

		{block title=%title}

                <div class="progress_bar_wrap clearfix">
                    <div class="progress_bar_bg">
                        <div class="progress_bar" {id="progressBar"}></div>
                    </div>
                    <div class="progress_bar_txt">{text %.components.profile_progress_bar.title} <span {id="index"}></span>%</div>
                </div>
                
		{menu type=block items=$pages}
		<br clear="all" />
		{form EditProfile}
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
									{if $field.name !='location'}
										<td class="label">
										  {label %.profile_fields.label_edit.`$field.id` for=$field.name}{if $field.required}<span class="required_star">*</span>{/if}
									    </td>
										<td class="value">{input name=$field.name labelsection=profile_fields.value}</td>

									{else}
									    <div style="display: none">
                                            {label for=$field.name}
                                        </div>
										<td colspan="2">{input name=$field.name labelsection=profile_fields.label_edit}</td>
									{/if}


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
			<p align="center">{button action=$form->active_action label=%.forms._actions.save}</p>
		{/form}
	{/block}
	{/container}
{/canvas}