{* Component Profile Preference *}
{canvas}
	{container class="profile_preference center_block" stylesheet="profile_preference.style"}
	
	<div style="display: none"><div {id="restore_confirm_content"}>{text %restore_confirmation}</div></div>
	{block title=%title}
		{form ProfilePreference}
		
			
				{foreach from=$preferences key=section_name item=section}
					<table class="form">
						<thead>
							<tr>
								<th colspan="2">
									{text %configs.`$section_name`.title}
								</th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$section key=name item=config}
								<tr>
									<td class="label">
										<b>
											{label %configs.`$section_name`.`$name`.title for=$name}
										</b>
										<div class="description">
											{text %configs.`$section_name`.`$name`.description}
										</div>
									</td>
									<td class="value">{input name="`$section_name`___`$name`"}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/foreach}
			<br>
			<p align="left" style="display:none"><a {id="restore_defaults_btn"} href="javascript://">{text %restore_defaults}</a></p>
			<p align="center">{button action=save}</p>
		{/form}
	{/block}
	{/container}
{/canvas}