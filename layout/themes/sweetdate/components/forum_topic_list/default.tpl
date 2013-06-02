{* component Forum Topic List *}
{canvas}
{container stylesheet="forum_topic_list.style"}

    {if $group_forum && $is_blocked}
        {block}
        <div class="no_content">
            {text %.components.group.is_blocked}
        </div>
        {/block}
	{elseif $group_forum && $group.browse_type == 'private' && !$is_member}
		<div class="no_content">
			{text %.components.group.for_members_only}<br />
			{component GroupJoin group_id=$group.group_id}
		</div>
	{else}
		<div class="action_buttons">
			{if $moderator && !$group_forum}
			<a href="{document_url doc_key='forum_banned_profile_list'}" class="float_left">
				[{text %.components.forum_banned_profile_list.link_banned_list}]
			</a>
			{/if}
			{if !$group_forum}{component ForumSearch}{/if} 
			
			{if $group_forum && $is_member && $no_permission}
				<div class="float_right">
	    			<input type="button" class="button" value="{text %new_topic_btn}" {id="new_topic_btn"}/>
	    		</div>
			{elseif !$group_forum && $profile_id && $no_permission}
	    		<div class="float_right">
	    			<input type="button" class="button" value="{text %new_topic_btn}" {id="new_topic_btn"}/>
	    		</div>
			{/if}
	    </div>
	    <br clear="all">
		{block title=%.nav_doc_item.titles.forum class="block_null"}
			<table class="form">
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th>{text %cap_topic}</th>
					<th class="topic_count">{text %cap_creator}</th>
					<th class="post_count">{text %cap_posts_count}</th>
					<th>{text %cap_last_post}</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$topics item=topic}
				<tr class="{cycle values='trc_1,trc_2'} list_item">
					<td class="{if $topic.is_closed=='y'}topic_block{/if}"></td>
					<td {if $topic.is_sticky=='y'}class="topic_stick"{/if}></td>
					<td class="topic_cap"><a href="{document_url doc_key='topic' topic_id=$topic.forum_topic_id}">{$topic.title|out_format:'forum'|censor:'forum'}</a></td>
					<td class="creator center">
						{if !$topic.is_deleted}<a href="{document_url doc_key='profile_view' profile_id=$topic.profile_id}">{$topic.username}</a>
						{else}{text %.label.deleted_member}{/if}
					</td>
					<td class="post_count center">{$topic.count_post}</td>
					<td class="last_post"><span class="small">by 
						{if !$topic.last_post_is_deleted}<a href="{document_url doc_key='profile_view' profile_id=$topic.last_post_profile_id}">{$topic.last_post_username}</a>
						{else}{text %.label.deleted_member}{/if}
						<br/>
						{$topic.last_post_stamp|spec_date}</span></td>
				</tr>
			{/foreach}
			</tbody>
			</table>
			{if !$topics}
			<div class="no_content">
				{text %no_topics}
			</div>
			{/if}
		{/block}
		<div class="forum_topic_descr clearfix">
		 <div class="topic_block_descr">{text %.components.forum_topic_list.locked_topic}</div>
		 <div class="topic_stick_descr">{text %.components.forum_topic_list.sticky_topic}</div>
		</div>
		{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
		
		{if $group_forum}
			{if !$is_member}
				<div class="no_content">{text %.components.group.add_topic}</div>
			{else}
				{component ForumAddTopic}
			{/if}
		{else}
			{if $profile_id}{component ForumAddTopic}{/if}	
		{/if}
	{/if}
{/container}
{/canvas}