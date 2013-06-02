
<div class="members">

	{if $conv_arr}
	{$paging->render()}
	<table class="mailbox_threads">
		{foreach from=$conv_arr item='conv'}
		{if ($conv.mails_count < 1 && $folder eq 'sentbox')}
		{else}
		<tr class="{cycle values='even,odd'}">
			<td {if $conv.new_msg eq 1 && $folder eq 'inbox'} 
					class="status new_msg" title="{text section='components.mailbox_conversations_list' key='new_messages'}"
				{elseif $conv.is_replied eq 'yes' && $conv.is_system eq 'no' && $folder eq 'inbox'} class="status replied_msg" title="{text section='components.mailbox_conversations_list' key='is_replied'}"
				
				{else}class="status"{/if}></td>
			<td class="auth_thumb">
				{if $conv.is_system eq 'yes'}
					<div class="system_msg_icon"></div>
				{elseif $folder=='sentbox'}
					{profile_thumb profile_id=$conv.recipient_id size=50 href=$conv.conv_href}					
				{else}
					{profile_thumb profile_id=$conv.sender_id size=50 href=$conv.conv_href}
				{/if}
			</td>
			<td class="list_item">
			<div class="subject_message">
				<span class="author">
				{if $conv.is_system eq 'yes'}
					{text section='components.mailbox_conversations_list' key='system_msg'}:
				{else}
					{if $folder eq 'sentbox'}{text section='components.mailbox_conversations_list' key='label_to'} {/if}
					{if $conv.username}<a href="{if $folder eq 'sentbox'}{profile_url profile_id=$conv.recipient_id}{else}{profile_url profile_id=$conv.sender_id}{/if}">{$conv.username}</a>{else}{text section='label' key='deleted_member'}{/if}:
				{/if}
				</span>
				<span class="bold">
					<a href="{$conv_url}{$conv.conversation_id}">
						{if $conv.mails_count > 1 && $conv.is_system eq 'no'}{text section='components.mailbox_conversations_list' key='label_re'} {/if}{$conv.subject}
					</a></span><br />
				{if $conv.is_system eq 'yes'}
					<span class="mess">{$conv.text|strip_tags|truncate:100|smile}</span>
				{elseif $check_rdbl and $conv.is_readable eq 'no' and $conv.sender_id != $profile_id}
					{$perm_msg}
				{elseif isset($perm_msg) and !$check_rdbl and $conv.sender_id != $profile_id}
					{$perm_msg}
				{else}
					<span class="mess">{$conv.text|strip_tags|truncate:100}</span>{*|smile*}
				{/if}
				<div class="conv_date">{$conv.last_message_ts|spec_date}</div>
			</div>
			</td>
		</tr>
		{/if}
		{/foreach}
	</table>
	{$paging->render()}
	
	{else}
		{if $folder eq 'inbox'}
			<div class="no_content">{text section='components.mailbox_conversations_list' key='no_incoming_msg'}</div>
		{elseif $folder eq 'sentbox'}
			<div class="no_content">{text section='components.mailbox_conversations_list' key='no_sent_msg'}</div>
		{/if}
	{/if}
	
</div>