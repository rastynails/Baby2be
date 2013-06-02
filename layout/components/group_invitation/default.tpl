
{canvas}
	{container}
		<div class="float_half_left wide">
			{block title=%sent_invitations}
				{foreach from=$invitations item='inv' name=i}
					<a href="{document_url doc_key='profile' profile_id=$inv.profile_id}">{$inv.username}</a>{if not $smarty.foreach.i.last}, {/if}
				{/foreach}
			{/block}

		{capture name="members_title"}
			{text %group_members} ({$members_count})
		{/capture}
			{block title=$smarty.capture.members_title}
				{foreach from=$members item='mem' name=m}
					<a href="{document_url doc_key='profile' profile_id=$mem.profile_id}">{$mem.username}</a>{if not $smarty.foreach.m.last}, {/if}
				{/foreach}
			{/block}			
		</div>
		
		<div class="float_half_right narrow">
			{block title=%invite_members}
				<div class="center">
				<br />{text %.components.group_edit.howtoadd}<br/><br />
				{form GroupInviteMembers}
					<span style="display: none">{label for="members"}</span>
					{input name=members class="area_small"}<br />
					{button action=invite}
				{/form}
				</div>
			{/block}
		</div>
	{/container}
{/canvas}