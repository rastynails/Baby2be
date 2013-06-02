{* component Thumbed Profile List *}

{container stylesheet='style.style'}
	<div class="thumb_list">
    {foreach from=$profiles item='profile'}
        <div>
            <a href="{$profile.url}">
                {profile_thumb profile_id=$profile.id username=true size='60'}
            </a>
        </div>
    {/foreach}
    <span style="display:block;clear:both"></span><br clear="all" />
    </div>
{/container}
