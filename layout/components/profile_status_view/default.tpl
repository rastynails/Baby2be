{container stylesheet="profile_status_view.style"}
<div class="psv_cont clearfix">
    <div class="psv_thumbnail float_left">
        {profile_thumb profile_id=$profile.id size=85}
    </div>
    <div class="psv_content float_left">
        <div class="psv_info">
            <span class="psv_sex">{$profile.sexLabel},</span>
            <span class="psv_age">{$profile.age} {text %.profile.labels.age},</span>
            <span class="psv_location">
                {if $profile.city}
                    {$profile.city},
                {elseif !empty($profile.custom_location)}
                    {$profile.custom_location},
                {/if}                
                {if $profile.state}
                    {$profile.state},
                {/if}
                {if $profile.country}
                    {$profile.country}
                {/if}
            </span>
        </div>
	        <div class="psv_status">
	            <span>{text %status}:</span> {$profile.status}
	        </div>
	    
        <div class="profile_activity">
            {if isset($profile.activity_info.online)}
                {text %.profile.labels.activity}:
                {online_btn profile_id=$profile.id}
            {elseif $profile.activity_info.item}
                {text %.profile.labels.activity}:
                {$profile.activity_info.item_num}&nbsp;{$profile.activity_info.item_label}
            {/if}
        </div>
    </div>
</div>
{/container}