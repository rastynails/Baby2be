{* component Forum Post List *}
{canvas}

{container stylesheet="forum_post_list.style"}

{if $group_forum && $is_blocked}
    {block}
    <div class="no_content">
        {text %.components.group.is_blocked}
    </div>
    {/block}
{else}

 <div class="float_left">
 {paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
 </div>

 <div class="clr"></div>


{component ForumTopic}

	<table width="100%" class="post_list">
		{foreach from=$posts item=post}
			<tr {id=post_`$post.forum_post_id`}>
				<td class="forum_thumb">
				    {profile_thumb profile_id=$post.profile_id size=60}
	                <p class="small">
	                	{if !$post.is_deleted}<a href="{document_url doc_key='profile_view' profile_id=$post.profile_id}">{$post.username}</a>
	                	{else}{text %.label.deleted_member}{/if}
	                </p>

	                {if !$post.is_deleted && !empty($SK.profile) && $SK.profile.id != $post.profile_id }
	                   <a class="forum_pm" href="{document_url doc_key="mailbox" folder=write username=$post.username}" title="{text %.label.pm}">&nbsp;</a>
	                {/if}
                </td>
				<td class="list_item">
					<div class="time small">
						{$post.create_stamp|spec_date}
						{if $post.action_buttons}
							| <a class="edit_post" href="javascript://">{text %edit_post}</a> &#183; <a class="delete_post" href="javascript://">{text %delete_post}</a>
						{/if}
					</div>
					<div class="post_text">{$post.text|smile|out_format:'forum'|censor:'forum'}</div>
					<div class="attachments">
                        {attachment entity="forum_post" entityId=$post.forum_post_id owner=$post.profile_id}
					</div>
					{if $post.edit_stamp}
						<div class="edited">{text %edited_by}
							{if !$post.edited_by_is_deleted}{$post.edited_by_username} {else}{text %.label.deleted_member} {/if}
							{$post.edit_stamp|spec_date}
						</div>
                    {/if}
                    <div class="system_buttons">
                        {if !$post.is_owner && !$post.is_deleted}{component Report type='forum' reporter_id=$profile_id entity_id=$post.forum_post_id}{/if}
                        {if $moderator && !$post.is_owner && !$post.is_deleted}<div class="report">
                    		<input type="button" class="button ban_profile" value="{text %ban_btn}" />
                    	</div>{/if}
                    	{if $topic_info.is_closed=='n' && $profile_id}
                    	<div class="float_right">
                    		<input type="button" class="button reply_post" value="{text %reply_btn}" />
                    	</div>
                    	{/if}
                    </div>
				</td>
			</tr>
		{/foreach}
	</table>

    <div class="float_left">
    {paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
    </div>
    {if $profile_id}
    <div class="topic_notify">
    	<input type="button" class="button topic_sub" value="{text %track_btn}" {if $subscribed}style="display: none;"{/if}/>
    	<input type="button" class="button topic_unsub" value="{text %unsub_track_btn}" {if !$subscribed}style="display: none;"{/if}/>
    </div>
	{/if}
	<br clear="all"/>

	{if $topic_info.is_closed=='n' && $profile_id}
    	{if $group_forum}
    		{if !$is_member}
				<div class="no_content">{text %.components.group.add_post}</div>
			{else}
				{component ForumAddPost}
			{/if}
    	{else}
    		{component ForumAddPost}
    	{/if}
	{elseif $profile_id}
		<div class="topic_is_closed">{text %topic_is_locked}</div>
	{/if}

	{if $profile_id}
	{*Edit Post Thickbox*}
 	<div style="display: none">
		<div class="editbox_title"><b>{text %labels.edit_box_title}</b></div>
		<div class="editbox_content">
		{form ForumEditPost}
			 <table class="form">
				<tr>
					<td class="value">{text_formatter for='edit_post_text' entity="forum"}</td>
				</tr>
				<tr>
					<td class="value">{input name="edit_post_text" class="area_big"}</td>
				</tr>
				<tr>
					<td class="submit">{button action="edit"}</td>
				</tr>
			 </table>
		{/form}
	 	</div>
	 </div>

	{*Delete Post Confirm*}
	<div style="display: none">
		<div {id="confirm_title"}>{text %labels.delete_confirm_title}</div>
		<div {id="confirm_content"}>{text %messages.delete_confirm_content}</div>
	</div>

	{*Track Replies Confirm*}
	<div style="display: none">
		<div {id="track_replies_title"}>{text %labels.track_replies_title}</div>
		<div {id="track_replies_content"}>{text %messages.track_replies_content}</div>
	</div>

	{*UnTrack Replies Confirm*}
	<div style="display: none">
		<div {id="unsub_title"}>{text %labels.unsub_track_replies_title}</div>
		<div {id="unsub_content"}>{text %messages.unsub_track_replies_content}</div>
	</div>
	{/if}

	{if $moderator}
	{*Ban Profile Thickbox*}
 	<div style="display: none">
		<div class="ban_pr_box_title"><b>{text %labels.ban_box_title}</b></div>
		<div class="ban_pr_box_content">
		{form ForumBanProfile}
			 <table class="form">
				<tr>
					<td class="label">{label for="period"}</td>
					<td class="value">{input name="period"}</td>
				</tr>
				<tr>
					<td colspan="2" class="submit">{button action="ban"}</td>
				</tr>
			 </table>
		{/form}
	 	</div>
	 </div>
	{/if}
{/if}
{/container}
{/canvas}