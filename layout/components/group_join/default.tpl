
{container stylesheet="group_join.style"}

	{if $group.join_type == 'open'}
		{if $display_type == 'link'}
			<a href="javascript://" {id="join_link"}>{text %join}</a>
		{else}
			<input type="button" value="{text %join}" {id="join_btn"} />
		{/if}
	{elseif $group.join_type == 'closed'}
		{if !$group.allow_claim}
			{text %by_invitation_only}
		{else}
			{text %to_join} <a href="javascript://" {id="claim_link"}>{text %claim}</a>
		{/if}
	{/if}
	
	
{/container}