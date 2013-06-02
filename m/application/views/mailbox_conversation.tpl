
<br />
{capture name='conversation_title'}
	{if $conv.is_system eq 'yes'}
		{text section="components.mailbox_conversations_list" key="system_msg"}
	{else}
		{text section="components.mailbox_conversation" key=conv_label}:
	{/if}
{/capture}

{block}
	<table>
		<tr>
			<td class="thumb">
				{if $conv.is_system eq 'yes'}
					<div class="system_msg_icon"></div>
				{else}
					{profile_thumb profile_id=$conv.initiator_id size=60}<br />
				{/if}
				<div class="center">
					{if $conv.opponent}
						<a href="{profile_url profile_id=$conv.initiator_id}">{$conv.initiator_username}</a>
					{elseif $conv.is_system eq 'no'}
						{text section=label key=deleted_member}
					{/if}
				</div>
			</td>
			<td class="conv_info">
				{$smarty.capture.conversation_title}
				<span class="highlight">{$conv.subject}</span><br />
				{if $conv.is_system eq 'no'}
					{text section=components.mailbox_conversation key=conv_between} {if $conv.opponent}<a href="{profile_url profile_id=$conv.opponent_id}">{$conv.opponent}</a>{else}{text section=label key=deleted_member}{/if}<br />
					<span class="small">{text section=components.mailbox_conversation key=conv_started} {$conv.conversation_ts|spec_date}</span>
				{else}
					<br /><span class="small">{text section=components.mailbox_conversation key=conv_sent} {$conv.conversation_ts|spec_date}</span>
				{/if}
				
				{if $conv.is_system eq 'no'}
					{if $conv.opp_status.show_status eq 'yes'}
						<div class="block_info">
						{if $conv.opp_status.is_read eq 'yes'}
							<a href="{profile_url profile_id=$conv.opponent_id}">{$conv.opponent}</a> {if $msg_count eq 1}{text section=components.mailbox_conversation key=status_read_msg}{else}{text section=components.mailbox_conversation key=status_read_msgs}{/if}
						{else}
							<a href="{profile_url profile_id=$conv.opponent_id}">{$conv.opponent}</a> {if $msg_count eq 1}{text section=components.mailbox_conversation key=status_unread_msg}{else}{text section=components.mailbox_conversation key=status_unread_msgs}{/if}
						{/if}
						</div>
					{/if}
					{if $conv.opp_status.is_available eq 'no' && $conv.opponent}
					<div class="block_info">
						{*text %status_cant_answer opponent=$conv.opponent*}
					</div>
					{/if}
				{/if}
			</td>
		</tr>
	</table>

	<div class="right">
		<a class="btn" href="{$unread_url}">{text section='forms.manage_conversation.actions' key='mark_unread'}</a>
		<a class="btn" href="{$delete_url}">{text section='forms.manage_conversation.actions' key='remove'}</a>
	</div>
{/block}
	
<table class="mailbox_threads">
	{foreach from=$messages item='msg'}
	<tr class="{cycle values='even,odd'}">
		<td class="auth_thumb">
			{if $conv.is_system eq 'yes'}
				<div class="system_msg_icon"></div>
			{else}
				{profile_thumb profile_id=$msg.sender_id size=50}
			{/if}
		</td>
		<td class="user_date">
			{if $conv.is_system eq 'no'}
				{if $msg.username}
					<a href="{profile_url profile_id=$msg.sender_id}">{$msg.username}</a>
				{else}
					{text section=label key=deleted_member}
				{/if}
			{/if}
			<br /><span class="small">{$msg.time_stamp|spec_date}</span>
		</td>
		<td class="subject_message list_item">
			{if $check_rdbl and $msg.is_readable eq 'no' and $msg.sender_id != $profile_id}
				{$perm_msg}
			{elseif isset($perm_msg) and !$check_rdbl and $msg.sender_id != $profile_id}
				{$perm_msg}
			{else}
				<span class="mess">{$msg.text|smile}{*|out_format:"mailbox"|*}</span>
			{/if}
		</td>
	</tr>
	{/foreach}
</table>

<br />
{if $conv.is_system eq 'no' && $conv.opponent}
<div class="form">
	<a name="reply"></a>
	<span>{text section='components.send_message' key='reply_label'}</span>
	<form method="post">
		<input type="hidden" name="conv_id" value="{$conv.conversation_id}" />
		<input type="hidden" name="sender_id" value="{$conv.new_msg_sender}" />
		<input type="hidden" name="recipient_id" value="{$conv.new_msg_recipient}" />
		<textarea name="message"></textarea>
		<div class="submit"><input type="submit" value="{text section='forms.send_message.actions' key='reply'}" /></div>
	</form>
</div>
{/if}