{canvas}
	{container stylesheet="profile_list.style" class="profile_list_contener"}
		{if $tabs}
				{menu type='tabs-small' items=$tabs}
		{/if}
		<br clear="all">
		<div class="no_content">{text %no_items}</div>
	{/container}
{/canvas}