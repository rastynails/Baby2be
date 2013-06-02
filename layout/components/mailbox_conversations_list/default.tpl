{* component MailboxConversationsList *}
{canvas}

{container text_ns='components.mailbox_conversations_list' stylesheet="mailbox_conversations_list.style"}
<br />
	{block_cap toolbar="&nbsp;"}
		{menu type="block" items=$mailbox_menu_items}
	{/block_cap}
	{block}
	{if $conv_arr}
	{form ManageMailbox mark_for=$profile_id}	
	<table class="mailbox_threads">
		{foreach from=$conv_arr item='conv'}
		{if ($conv.mails_count < 1 && $folder=='sentbox')}
		{else}
		<tr>
			<td {if $conv.new_msg eq 1 && $folder eq 'inbox'} 
					class="status new_msg" title="{text %new_messages}"
				{elseif $conv.is_replied eq 'yes' && $conv.is_system eq 'no' && $folder eq 'inbox'} class="status replied_msg" title="{text %is_replied}"
				
				{else}class="status"{/if}></td>
			<td class="chbox">{input_item name="conversations" value=$conv.conversation_id}</td>
			<td class="auth_thumb">
				{if $conv.is_system eq 'yes'}
					<div class="system_msg_icon"></div>
				{elseif $folder=='sentbox'}
					{profile_thumb profile_id=$conv.recipient_id size=60 href=$conv.conv_href}					
				{else}
					{profile_thumb profile_id=$conv.sender_id size=60 href=$conv.conv_href}
				{/if}
			</td>
			<td id="user_date">
				{if $conv.is_system eq 'yes'}
					{text %system_msg}
				{else}
					{if $folder eq 'sentbox'}{text %label_to} {/if}
					{if $conv.username && $conv.username != 'username'}<a href="{if $folder eq 'sentbox'}{document_url doc_key='profile' profile_id=$conv.recipient_id}{else}{document_url doc_key='profile' profile_id=$conv.sender_id}{/if}">{$conv.username}</a>{else}{text %.label.deleted_member}{/if}
				{/if}
				<br />{$conv.last_message_ts|spec_date}
			</td>
			<td class="subject_message list_item">
				<span class="bold">
					<a href="{document_url doc_key='mailbox_conversation' conv_id=$conv.conversation_id}">
						{if $conv.mails_count > 1 && $conv.is_system eq 'no'}{text %label_re} {/if}{$conv.subject|out_format:"mailbox"|censor:"mailbox"}
					</a></span><br />
				{if $conv.is_system eq 'yes'}
					<span class="mess">{$conv.text|smile|truncate:150|censor:"mailbox"}</span>
				{elseif $check_rdbl and $conv.is_readable eq 'no' and $conv.sender_id != $profile_id}
					{$perm_msg}
				{elseif isset($perm_msg) and !$check_rdbl and $conv.sender_id != $profile_id}
					{$perm_msg}
				{else}
					<span class="mess">{$conv.text|out_format:"mailbox"|truncate:150|smile|censor:"mailbox"}</span>
				{/if}
			</td>
			<td class="actions list_item">
			{if $conv.is_system eq 'no'}
				{component MailboxControls profile_id=$conv.sender_id conversation_id=$conv.conversation_id folder=$folder}
			{else}
				{component MailboxControls profile_id=$conv.sender_id conversation_id=$conv.conversation_id folder='sentbox'}
			{/if}
			</td>
		</tr>
		{/if}
		{/foreach}
		<tr>
			<td></td>
			<td class="chbox"><input type="checkbox" id="select_all" /></td>
			<td colspan="2"><label for="select_all">{text %select_all}</label></td>
			<td class="subject_message no_border" colspan="2">
				{text %with_selected}: {button action='mark_unread'} 
				{button action='mark_read'}
				{button action='remove' class="delete"}
			</td>
		</tr>
	</table>
	{/form}
	{else}
		{if $folder eq 'inbox'}
			<div class="no_content">{text %no_incoming_msg}</div>
		{elseif $folder eq 'sentbox'}
			<div class="no_content">{text %no_sent_msg}</div>
		{/if}
	{/if}
	{/block}
	{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
{/container}
{/canvas}
