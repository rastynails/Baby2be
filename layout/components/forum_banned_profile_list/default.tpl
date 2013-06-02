{* component Forum Banned Profile List *}
{canvas}

<div class="action_buttons">
	{component ForumSearch}
</div>
<br/>
{container stylesheet="forum_banned_profile_list.style"}
{block title=%banned_profiles class="block_null"}
<table class="form">
	<thead>
		<tr>
			<th class="ban_list">{text %cap_username}</th>
			<th class="ban_list">{text %cap_expiration_time}</th>
			<th>{text %cap_remove_ban}</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$profile_list.profiles item=profile}
		<tr {id=profile_`$profile.profile_id`} class="list_item {if $profile_id==$profile.profile_id}you_banned{/if}">
			<td>{if !$profile.is_deleted}{$profile.username}{else}{text %.label.deleted_member}{/if}</td>
			<td>{$profile.expiration_stamp|spec_date}</td>
			<td>{if $moderator}
				<a href="javascript://">[{text %remove_ban_btn}]</a>
			{/if}</td>
		</tr>
	{/foreach}
	</tbody>
</table>

{if !$profile_list.profiles}
	<div class="no_content">{text %no_profile}</div>
{/if}

{/block}

{/container}
{/canvas}