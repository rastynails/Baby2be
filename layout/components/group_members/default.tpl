
{canvas}
	{container}
		{if $is_blocked}
			<div class="center_block">
			{block title=$group.title|out_format|censor:'group':true}
				<div class="no_content">{text %.components.group.is_blocked}</div>
			{/block}
			</div>
		{elseif $group.browse_type == 'private' && !$is_member && !$show_invitation}
			{block title=$group.title|censor:'group':true}
				<div class="no_content">{text %.components.group.for_members_only}</div>
			{/block}
		{else}
			{component $group_members}
		{/if}
	{/container}
{/canvas}