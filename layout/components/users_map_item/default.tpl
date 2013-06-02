{canvas}

    {container }
<div class="clearfix">
    <div class="profile_list_thumb">
        <div style="position: relative; z-index: 98">
          {profile_thumb profile_id=$profile.profile_id size=40}
        </div>
        <div class="membership_icon">{membership_icon profile_id=$profile.profile_id}</div>
    </div>
    <div class="profile_list_info">
        <div class="loc_username"><a href="{$profile.profile_url}">{$profile.username}</a></div>
        {foreach from=$profile.age item=age name=age_values }
            {$age}{if !$smarty.foreach.age_values.last},{/if}
        {/foreach}
        <div class="loc_value">
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
        </div>

    </div>
</div>
    {/container}
{/canvas}