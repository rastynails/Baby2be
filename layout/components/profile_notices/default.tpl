{* component Attention *}

{container class="profile_notice_cont"}
	{block title=%title class="indicator"}
		<ul class="simple">
			{if $friend_requests}
				<li>
					{text %new}
					<a href="{document_url doc_key='profile_friend_list' tab='got_requests'}">
						<b>{text %requests}</b>
					</a>
					({$friend_requests})
				</li>
			{/if}
			{if $new_messages}
				<li>
					{text %new}
					<a href="{document_url doc_key='mailbox' folder='inbox'}"><b>{text %messages}</b></a>
					({$new_messages})
				</li>
			{/if}
			{if $invites_count}
				<li>
					{text %new}
					{text %invitations}
					({$invites_count}):
					<ul>
					{foreach from=$invites item=invite name=i}
						<li> - <a href="{document_url doc_key='group' group_id=$invite.group_id}"><b>{$invite.title|out_format|truncate:50|censor:'group':true}</b></a></li>
					{/foreach}
					<ul>
				</li>
			{/if}
		</ul>
	{/block}
{/container}