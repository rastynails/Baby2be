
{container stylesheet='profile_brief_info.style'}
<div class="clearfix">
    <div class="brief_info_thumb">
		{profile_thumb profile_id=$profile.profile_id size=60}
		<div class="profile_activity">
			{if isset($profile.activity_info.online)}
				{text %.profile.labels.activity}:<br>
				{online_btn profile_id=$profile.profile_id}
			{elseif $profile.activity_info.item}
				{text %.profile.labels.activity}:<br />
				{$profile.activity_info.item_num}&nbsp;{$profile.activity_info.item_label}
			{/if}
		</div>
	</div>
	<div class="brief_info_cont">
	       {$profile.sex_label}{foreach from=$profile.age item=item}, {$item}, {/foreach}
                {if $profile.location.city}
                        {$profile.location.city},
                {elseif !empty($profile.location.custom_location)}
                        {$profile.location.custom_location},
                {/if}

                {if $profile.location.state}
                        {$profile.location.state},
                {/if}

                {if $profile.location.country}
                        {$profile.location.country}.
                {/if}
		<br /><br />
		{if $viewer && $viewer != $profile.profile_id}
			<div class="member_actions">
				{component ProfileActions profile_id=$profile.profile_id}
			</div>
		{/if}
	</div>
</div>
{/container}