
<div class="members">
    {if !empty($service_msg)}
        <div class="not_found">{$service_msg}</div>
	{elseif isset($no_profiles)}
		<div class="not_found">{$no_profiles}</div>
	{else}
	{$paging->render()}
	<ul>
	{foreach from=$list item='member'}
		<li class="{cycle values='even,odd'}">
			<div class="thumbnail small">
				{profile_thumb profile_id=$member->profile_id size=60}
				
				{if isset($member->activity_info.online)}
					{online_btn profile_id=$member->profile_id display='link'}
				{elseif $member->activity_info.item}
					{$member->activity_info.item_num}&nbsp;{$member->activity_info.item_label}
				{/if}
			</div>
			<div class="pr_info">
				<div class="smallmargin">
					<span class="bottomborder">
						<a href="{$profile_url}{$member->username}">{$member->username}</a> &#183; {$member->sex} {$member->age} &#183;
						{if $member->location.city}{$member->location.city}, {/if}
						{if $member->location.state}{$member->location.state}, {/if}
						{if $member->location.country}{$member->location.country}{/if}
					</span>
				</div>
				{$member->general_description|strip_tags|nl2br|truncate:80}
			</div>
			<br clear="all" />
		</li>
	{/foreach}
	</ul>
	{$paging->render()}
	{/if}
</div>