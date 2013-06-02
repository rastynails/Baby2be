
{canvas}
{container}
	{if $is_blocked}
		<div class="center_block">
		{block title=$group.title|out_format|truncate:55|censor:'group':true}
			<div class="no_content">{text %is_blocked}</div>
		{/block}
		</div>
	{elseif $group.browse_type == 'private' && !$is_member && !$show_invitation && !$is_site_moderator}
		{block title=$group.title|out_format|truncate:55|censor:'group':true}
			<div class="no_content">{text %for_members_only}<br />{component GroupJoin group_id=$group.group_id}</div>
		{/block}
	{else}
		{if $is_creator || $is_moderator}
			<div class="right clearfix smallmargin">
				{if $group.join_type == 'closed'}
					{if $group.allow_claim}<input type="button" value="{text %view_claims}" {id="claims_btn"} />{/if}
					<input type="button" value="{text %invite_members}" {id="invite_btn"} />
				{/if}
			{if $is_creator}
				<input type="button" value="{text %send_mails}" {id="massmailing_btn"} />
				<input type="button" value="{text %edit_group}" {id="edit_btn"} />
			{/if}
			</div>
		{/if}
			
		<div class="float_half_left wide">
		{if $group.browse_type == 'private' && !$is_member && !$is_site_moderator}
			{block title=$group.title|out_format|truncate:48|censor:'group':true}
			<div class="no_content">{text %for_members_only}</div>
			{/block}
		{else}
			{block title=$group.title|out_format|truncate:48|censor:'group':true}
				<div style="overflow: hidden">{$group.description|censor:'group'|smile|out_format}</div>
				<br />
				<div class="no_content">
					{component GroupJoin group_id=$group.group_id}
					{if $is_member && !$is_creator}<input type="button" value="{text %leave_group}" {id="leave_group"} />{/if}
				</div>
			{/block}
			
			{component $group_forum}
			
			{component $group_comments}
		{/if}
		</div>
		
		<div class="float_half_right narrow">
		{if $show_invitation}
			{block class="indicator" title=%attention}
			<div class="center">
				{text %invitation_msg}<br /><br /> 
				<input type="button" {id="btn_accept"} value="{text %accept}" />
				<input type="button" {id="btn_decline"} value="{text %decline}" /><br /><br />
			</div>
			{/block}
		{/if}
		{if $group.browse_type == 'public' || $is_member || $is_site_moderator}
			{block}
				{if $group_image}<div class="center" style="overflow: hidden"><img src="{$group_image}" width="320" /></div>{/if}
				<div style="overflow: hidden">
				<table class="form">
		            <tr>
		                <td class="label">{text %group_title}</td>
		                <td class="value">{$group.title|out_format|censor:'group':true}</td>
		            </tr>		
					<tr>
						<td class="label">{text %type}</td>
						<td class="value">
							{text %browse_type_`$group.browse_type`}<br />
							{text %join_type_`$group.join_type`}
						</td>
					</tr>
					<tr>
						<td class="label">{text %members_count}</td>
						<td class="value">{$group.members_count}</td>
					</tr>		
					<tr>
						<td class="label">{text %creator}</td>
						<td class="value"><a href="{document_url doc_key='profile' profile_id=$group.profile_id}">{$group.username}</a></td>
					</tr>
					<tr>
						<td class="label">{text %moderators}</td>
						<td class="value">
							{foreach from=$moderators item='mod' name=m}
								<a href="{$mod.href}">{$mod.username}</a>{if not $smarty.foreach.m.last}, {/if}
							{/foreach}
						</td>
					</tr>
				</table>
				</div>
				
				<div class="no_content">{component GroupJoin group_id=$group.group_id}</div>
			{/block}
			
			{capture name="view_all"}
				<a href="{document_url doc_key='group_members' group_id=$group.group_id}">{text %view_all}</a>
			{/capture}
			{block title=%group_users toolbar=$smarty.capture.view_all}
				{if count($members)}
					{component SimpleList items=$members count=10}
				{else}
					{text %no_users}
				{/if}
			{/block}
		{/if}
		</div>
		<br clear="all" />
	{/if}
{/container}
{/canvas}