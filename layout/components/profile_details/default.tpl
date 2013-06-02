{* Component Profile Details *}

{container}
	{capture name=title}
		{text %title username=$username|truncate:18:"...":true}
	{/capture}
	{block title=$smarty.capture.title}

		<div class="page_cont overflow_hidden">

			<div class="menu prototype_node">{menu type="block" items=$menu_items}</div>
			<br clear="all" />
			{foreach from=$page_fields key="page_id" item="page"}
				<div class="page page_{$page_id}" style="display: none">
					<table class="form small">
						{foreach from=$page key="order" item="section"}
							<tr>
								<th colspan="2">
									{text %.profile_fields.section.`$section.sectionId`}
								</th>
							</tr>
							{foreach from=$section.fields key="order" item="field"}
								<tr>
								<td class="label">{text %.profile_fields.label_view.`$field.id`}</td>
								{if $field.type=="text"}
									<td class="value">
										{if $field.presentation=="url"}
											<a href="http://{$field.value|replace:'http://':''}" target="_blank">{$field.value}</a>
										{elseif $field.presentation=="callto"}
											<a href="callto:{$field.value}">{$field.value|censor:'profile'}</a>
										{elseif $field.presentation=="email"}
											<a href="mailto:{$field.value}">{$field.value}</a>
										{else}
											<div style="word-wrap: break-word; width: 295px">
											    {$field.value|censor:'profile'|nl2br}
											</div>
										{/if}
									</td>

								{elseif $field.type=="array"}
									<td class="value">
										{strip}
											{foreach from=$field.value item=item name="f"}
												{if $field.match}
													{text %.profile_fields.value.`$field.match`_`$item`}
												{else}
													{text %.profile_fields.value.`$field.name`_`$item`}
												{/if}
												{if !$smarty.foreach.f.last}, {/if}
											{/foreach}
										{/strip}
									</td>
								{/if}
								</tr>
							{/foreach}
						{/foreach}
					</table>
				</div>
				<div class="clr_div"></div>
			{/foreach}
		</div>
	{/block}
	<div class="clr_div"></div>
{/container}