{* component Forum Group List *}
{canvas}

<div class="action_buttons">
	{if $moderator}
	<a href="{document_url doc_key='forum_banned_profile_list'}" class="float_left">
		[{text %.components.forum_banned_profile_list.link_banned_list}]
	</a>
	{/if}
	{component ForumSearch}
</div>
<br/>
{container stylesheet="forum_group_list.style"}
    {foreach from=$groups item=group}
	{block title=$group.name|censor:group:true class="block_null"}
		<table class="form">
		<thead>
			<tr>
				<th colspan="2">{text %cap_forum}</th>
				<th class="topic_count">{text %cap_topics_count}</th>
				<th class="post_count">{text %cap_posts_count}</th>
				<th>{text %cap_last_topic}</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$group.forums item=forum}
			<tr class="{cycle values='trc_1,trc_2'} list_item">
				<td class="status"></td>
				<td><a href="{document_url doc_key='forum' forum_id=$forum.forum_id}" class="bold">{$forum.name}</a><br />
					<span class="small">{$forum.description}
				</span></td>
				<td class="topic_count center">{$forum.count_topic}</td>
				<td class="post_count center">{$forum.count_post}</td>
				<td class="last_post">
				{if $forum.last_topic_id}
				<span class="small">
					<a href="{document_url doc_key='topic' topic_id=$forum.last_topic_id}">{$forum.title|strip_tags|censor:forum:true}</a><br/>
					by {if !$forum.is_deleted}<a href="{document_url doc_key='profile_view' profile_id=$forum.profile_id}">{$forum.username}</a>
					   {else}{text %.label.deleted_member}{/if}
					<br />
					{$forum.last_topic_stamp|spec_date}
				</span>
				{/if}
				</td>
			</tr>
		{/foreach}
		</tbody>
		</table>
	{/block}
	{/foreach}
	
{/container}
{/canvas}