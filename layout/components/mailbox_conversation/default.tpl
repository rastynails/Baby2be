{* component MailboxConversation *}
{canvas}
{container stylesheet='mailbox_conversation.style' class="conversation" text_ns='components.mailbox_conversation'}

<br />
{capture name='conversation_title'}
	{if $conv.is_system eq 'yes'}
		{text %.components.mailbox_conversations_list.system_msg}
	{else}
		{text %conv_label}
	{/if}
{/capture}


{block_cap title=$smarty.capture.conversation_title|censor:"mailbox":true}
	{menu type="block" items=$mailbox_menu_items}
{/block_cap}

	{block}
		<table class="thumb_text_list">
			<tr>
				<td class="thumb">
					{if $conv.is_system eq 'yes'}
						<div class="system_msg_icon"></div>
					{else}
						{profile_thumb profile_id=$conv.initiator_id size=60}<br />
					{/if}
					{if $conv.is_system eq 'yes'}
					{elseif $conv.initiator_username && $conv.initiator_username !='username'}
					    <a href="{document_url doc_key='profile' profile_id=$conv.initiator_id}">
                        {$conv.initiator_username}</a>
					{else}
						{text %.label.deleted_member}
					{/if}
				</td>
				<td class="listing">
					<span class="bold">{$conv.subject|out_format:"mailbox"}</span><br />
					{if $conv.is_system eq 'no'}
						{text %conv_between} {if $conv.opponent}<a href="{document_url doc_key='profile' profile_id=$conv.opponent_id}">{$conv.opponent}</a>{else}{text %.label.deleted_member}{/if}<br /><br />
						<span class="small">{text %conv_started} {$conv.conversation_ts|spec_date}</span>
					{else}
						<br /><span class="small">{text %conv_sent} {$conv.conversation_ts|spec_date}</span>
					{/if}
				</td>
				{if $conv.is_system eq 'no'}
				<td class="listing conv_status">
					{if $conv.opponent && $conv.opp_status.show_status eq 'yes'}
						<div class="block_info">
						{if $conv.opp_status.is_read eq 'yes'}
							<a href="{document_url doc_key='profile' profile_id=$conv.opponent_id}">{$conv.opponent}</a> {if $msg_count eq 1}{text %status_read_msg}{else}{text %status_read_msgs}{/if}
						{else}
							<a href="{document_url doc_key='profile' profile_id=$conv.opponent_id}">{$conv.opponent}</a> {if $msg_count eq 1}{text %status_unread_msg}{else}{text %status_unread_msgs}{/if}
						{/if}
						</div>
					{/if}
					{if $conv.opp_status.is_available eq 'no' && $conv.opponent}
					<div class="block_info">
						{text %status_cant_answer opponent=$conv.opponent}
					</div>
					{/if}
				</td>
				{/if}
			</tr>
		</table>
			{if $msg_count >= 5}
			<div class="right">
				{form ManageConversation conversation_id=$conv.conversation_id mark_for=$profile_id}
					{button action='mark_unread'}
					{button action='remove'}
				{/form}
			</div>
			{/if}
	{/block}

	{block title=" "}
	<table class="mailbox_threads">
		{foreach from=$messages item='msg'}
		<tr>
			<td class="auth_thumb">
				{if $conv.is_system eq 'yes'}
					<div class="system_msg_icon"></div>
				{else}
					{profile_thumb profile_id=$msg.sender_id size=60}
				{/if}
			</td>
			<td id="user_date">
				{if $conv.is_system eq 'yes'}
					{text %.components.mailbox_conversations_list.system_msg}
				{else}
					{if $msg.username}
						<a href="{document_url doc_key='profile' profile_id=$msg.sender_id}">{$msg.username}</a>
					{else}
						{text %.label.deleted_member}
					{/if}
				{/if}
				<br />{$msg.time_stamp|spec_date}
			</td>
			<td class="subject_message list_item">
				{if $conv.is_system eq 'yes'}
					<span class="mess">{$msg.text|out_format:"mailbox"|censor:"mailbox"|smile}</span>
				{elseif $check_rdbl and $msg.is_readable eq 'no' and $msg.sender_id != $profile_id}
					{$perm_msg}
				{elseif isset($perm_msg) and !$check_rdbl and $msg.sender_id != $profile_id}
					{$perm_msg}
				{else}
					{if $msg.sender_id != $profile_id && $conv.is_system eq 'no'}
						<div class="make_report">{component Report type='message' reporter_id=$profile_id entity_id=$msg.message_id show_link=yes}</div>
					{/if}
					<span class="mess">{$msg.text|out_format:"mailbox"|censor:"mailbox"|smile}</span>
				{/if}
			</td>
		</tr>
		{/foreach}	
		<tr>
			<td colspan="2"></td>
			<td class="subject_message no_border">
			<div class="right">
				{form ManageConversation conversation_id=$conv.conversation_id mark_for=$profile_id}
					{button action='mark_unread'}
					{button action='remove'}
				{/form}
			</div>
				<br />
				{if $conv.is_system eq 'no' && $conv.opponent}
					<a name="reply"></a>
					{component SendMessage conversation_id=$conv.conversation_id sender_id=$conv.new_msg_sender recipient_id=$conv.new_msg_recipient type='reply'}
				{/if}
			</td>
		</tr>
	</table>
	{/block}
	{*paging total=$paging.total on_page=$paging.on_page pages=$paging.pages*}

{/container}
{/canvas}