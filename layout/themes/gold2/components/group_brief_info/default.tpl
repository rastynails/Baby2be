
{container stylesheet="group_brief_info.style"}
	<br />
	{block title=%about}
		<div class="group_image"><img src="{$group_image}" /></div>
		<div class="group_descr">
			<h3><a href="{document_url doc_key='group' group_id=$group.group_id}">{$group.title|out_format|truncate:100}</a><br /></h3>
			{$group.description|smile|out_format|truncate:1000|censor:'group'}<br />
			<br />
			{text %.components.group.browse_type_`$group.browse_type`}<br />
			{text %.components.group.join_type_`$group.join_type`}<br />
			
			<div class="group_actions">
				{text %.components.group_list.members}: <span class="highlight">{$members_count}</span>
				| <a href="{document_url doc_key='group' group_id=$group.group_id}">{text %group_index}</a>
				{if $can_join}| {component GroupJoin display_type='link' group_id=$group.group_id}{/if}
			</div>
		</div>
		<div class="clr"></div>
	{/block}
{/container}