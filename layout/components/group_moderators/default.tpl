
{container}
	{block title=%.components.group_edit.group_moderators}
		{if $moderators}
			<ul class="moderators">
			{foreach from=$moderators item='mod'}
				<li>
					<a href="{document_url doc_key='profile' profile_id=$mod.profile_id}">{$mod.username}</a> 
					<span class="small">(<a href="javascript://" rel="{$mod.profile_id}">{text %delete}</a>)</span>
				</li>
			{/foreach}
			</ul>
		{else}
			<div class="no_content">{text %.components.group_edit.no_moderators}</div>
		{/if}
	{/block}
	
	{block title=%.components.group_edit.moderators_add}
		<div class="center">
			{text %.components.group_edit.howtoadd}<br/><br />
			{form GroupAddModerators}
				{input name=moderators class='mod_add_input'}<br />
				<span style="display: none">{label for="moderators"}</span>
				{button action=add}
			{/form}
		</div>
	{/block}
{/container}	