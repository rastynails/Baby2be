{* component Forum Topic *}

{container stylesheet="forum_topic.style"}

	<div class="action_buttons">
		{if !$group_forum}
			{if $moderator}
			<a href="{document_url doc_key='forum_banned_profile_list'}" class="float_left">
				[{text %.components.forum_banned_profile_list.link_banned_list}]
			</a>
			{/if}
			{component ForumSearch}
			{if $topic_info.is_closed=='n' && $profile_id && $no_permission}
			<div class="float_right">
	    		<input type="button" class="button new_post" value="{text %new_post_btn}" {id="new_post_btn"}/>
	    	</div>	
    		{/if}
    	{else}
	    	{if $topic_info.is_closed=='n' && $is_member && $no_permission}
				<div class="float_right">
		    		<input type="button" class="button new_post" value="{text %new_post_btn}" {id="new_post_btn"}/>
		    	</div>	
    		{/if}
		{/if}
		
    	<br clar="all" />
    </div>
    <br />
	{block title=$topic_info.title|out_format:'forum'|censor:'forum':true class="topic_block"}
	{if !$cur_page || $cur_page==1}
		<table width="100%" class="topic_tab">
			<tr>
				<td class="forum_thumb">{profile_thumb profile_id=$topic_info.profile_id size=60}
                <p class="small">
                	{if !$topic_info.is_deleted}<a href="{document_url doc_key='profile_view' profile_id=$topic_info.profile_id}">{$topic_info.username}</a>
                	{else}{text %.label.deleted_member}{/if}
                </p>
                {if !$topic_info.is_deleted && !empty($SK.profile) && $SK.profile.id != $topic_info.profile_id }
                   <a class="forum_pm" href="{document_url doc_key="mailbox" folder=write username=$topic_info.username}" title="{text %.label.pm}"> </a>
                {/if}
                </td>
				<td class="list_item">
					{if !$group_forum}
					{if $profile_id==$topic_info.profile_id || $moderator}
						<div class="ctr_buttons">
						<div class="expandable_menu">
						{block expandable='yes' title=%topic_actions id="topic_action"}
			           	<ul class="menu-vertical">
	                        {if $profile_id==$topic_info.profile_id || $moderator}
	                        	<li class="ctr_btn"><a class="edit_topic" href="javascript://">{text %edit_btn}</a></li>
	                        	<li class="ctr_btn"><a class="delete_topic" href="javascript://">{text %delete_btn}</a></li>
	                   			<li class="ctr_btn">
	                   				<a class="lock_topic" href="javascript://" {if $topic_info.is_closed=='y'}style="display: none;"{/if}>{text %lock_btn}</a>
	                   				<a class="unlock_topic" href="javascript://" {if $topic_info.is_closed=='n'}style="display: none;"{/if}>{text %unlock_btn}</a> 
	                        	</li>
	                        {/if}                 
	                        {if $moderator}	
	                        	<li class="ctr_btn">
	                        		<a class="sticky_topic" href="javascript://" {if $topic_info.is_sticky=='y'}style="display: none;"{/if}>{text %sticky_btn}</a>
	                        		<a class="unsticky_topic" href="javascript://" {if $topic_info.is_sticky=='n'}style="display: none;"{/if}>{text %unsticky_btn}</a>
	                        	</li>
	                        	<li class="ctr_btn"><a {id="move_topic_btn"} href="javascript://">{text %move_topic_btn}</a></li>
	                        {/if}
	                    </ul>
	                    {/block}
	                    </div>
	                    </div>
						<br clear="both"/>
					{/if}
					
					{else}
						{if $profile_id==$topic_info.profile_id || $group_moderator}
						<div class="ctr_buttons">
						<div class="expandable_menu">
						{block expandable='yes' title=%topic_actions id="topic_action"}
			           	<ul class="menu-vertical">
                        	<li class="ctr_btn"><a class="edit_topic" href="javascript://">{text %edit_btn}</a></li>
                        	<li class="ctr_btn"><a class="delete_topic" href="javascript://">{text %delete_btn}</a></li>
                   			<li class="ctr_btn">
                   				<a class="lock_topic" href="javascript://" {if $topic_info.is_closed=='y'}style="display: none;"{/if}>{text %lock_btn}</a>
                   				<a class="unlock_topic" href="javascript://" {if $topic_info.is_closed=='n'}style="display: none;"{/if}>{text %unlock_btn}</a> 
                        	</li>                 
	                    </ul>
	                    {/block}
	                    </div>
	                    </div>
						<br clear="both"/>
						{/if}
					{/if}
					
					<div class="time small">{$topic_info.create_stamp|spec_date}</div>
					<div class="post_text">{$topic_info.text|smile|out_format:'forum'|censor:'forum'}</div>
					<div class="attachments">
                        {attachment entity="forum_topic" entityId=$topic_info.forum_topic_id owner=$topic_info.profile_id}
                    </div>
					{if $topic_info.edit_stamp}
						<div class="edited">{text %edited_by} {if !$topic_info.edited_by_is_deleted}{$topic_info.edited_by_username}
															  {else}{text %.label.deleted_member}{/if} 
											{$topic_info.edit_stamp|spec_date}</div>
                    {/if}
                    <div class="system_buttons">
                    	{if !$group_forum}
                            {if $profile_id != $topic_info.profile_id}
	                            {component Report type='forum' reporter_id=$profile_id entity_id=$topic_info.forum_post_id}
                            {/if}
                            {if $moderator && $profile_id != $topic_info.profile_id}<div class="report">
	                    		<input type="button" class="button ban_profile" value="{text %ban_profile_btn}" />
	                    	</div>{/if}                   
	                    	{if $topic_info.is_closed=='n' && $profile_id}	
	                    		<input type="button" class="button reply_post" value="{text %reply_btn}" />
	                    	{/if}
                    	{else}
                    		{if $topic_info.is_closed=='n' && $is_member}	
	                    		<input type="button" class="button reply_post" value="{text %reply_btn}" />
	                    	{/if}
                    	{/if}
                    </div>
				</td>
			</tr>
		</table>
	{/if}
	{/block}

{if $profile_id==$topic_info.profile_id || $moderator || $group_forum && $group_moderator}	
	{* Edit Topic Thickbox *}
	<div style="display: none;">
		<div class="edit_topic_title"><b>{text %labels.edit_topic_title}</b></div>
		<div class="edit_topic_content">			
		{form ForumEditTopic}
			<table class="form">
				<tr>
					<td colspan="2" class="label">{text_formatter for='first_post' entity="forum"}</td>
				</tr>			
				<tr>
					<td class="label">{label for="title"}</td>
					<td class="value all_row_width">{input name="title"}</td>
				</tr>
				<tr>
					<td class="label">{label for="first_post"}</td>
					<td class="value">
						{input name="first_post" class="area_big"}
					</td>
				</tr>
			    <tr><td colspan="2" class="submit">{button action="save"}</td></tr>
			 </table>
	 	{/form}
		</div>
	 </div>
		 
	{* Delete Topic Confirm *}	 
	<div style="display: none">
		<div {id="delete_confirm_title"}>{text %labels.delete_confirm_title}</div>
		<div {id="delete_confirm_content"}>{text %messages.delete_confirm_content}</div>
	</div>	
	
	{* Lock Topic Confirm *}	 
	<div style="display: none">
		<div {id="lock_confirm_title"}>{text %labels.lock_confirm_title}</div>
		<div {id="lock_confirm_content"}>{text %messages.lock_confirm_content}</div>
	</div>	
		
	{* UnLock Topic Confirm *}	 
	<div style="display: none">
		<div {id="unlock_confirm_title"}>{text %labels.unlock_confirm_title}</div>
		<div {id="unlock_confirm_content"}>{text %messages.unlock_confirm_content}</div>
	</div>		
{/if}	
	
{if $moderator}	 
	{* Move Topic Thickbox *}
	<div style="display: none;">
		<div class="move_topic_title"><b>{text %labels.move_topic_title}</b></div>
		<div class="move_topic_content">			
		{form ForumMoveTopic}
			<div class="value">{input name="to_forum_id"}</div>
			<div class="move_topic_submit">{button action="move"}</div>
	 	{/form}
		</div>
	</div>	
	 
	
	{* Sticky Topic Confirm *}	 
	<div style="display: none">
		<div {id="sticky_confirm_title"}>{text %labels.sticky_confirm_title}</div>
		<div {id="sticky_confirm_content"}>{text %messages.sticky_confirm_content}</div>
	</div>

	{* UnSticky Topic Confirm *}	 
	<div style="display: none">
		<div {id="unsticky_confirm_title"}>{text %labels.unsticky_confirm_title}</div>
		<div {id="unsticky_confirm_content"}>{text %messages.unsticky_confirm_content}</div>
	</div>	
{/if}	
{/container}
