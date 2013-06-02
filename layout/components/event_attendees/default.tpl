{* component Event Attendees *}

{container}
	{capture name='attendees'}
		{text %.event.attendees}: {$count}
	{/capture}
	
	{block_cap title=$smarty.capture.attendees}{/block_cap}  
    {if $view_all_link}
        <div class="block_toolbar">
            <a href="{$view_all_link}">{text %.event.view_all}</a>
        </div>
    {/if}
    {block}
		{if $false_label}
        	<div class="center"></div>
        {else}
			{component $profile_list}
        {/if}
    {/block}
{/container}
