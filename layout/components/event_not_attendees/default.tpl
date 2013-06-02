{* component Event Attendees *}

{container}
	{capture name='not_attendees'}
		{text %.event.not_attendees}: {$count}
	{/capture}

	{block_cap title=$smarty.capture.not_attendees}{/block_cap}
    {if $view_all_link}
    <div class="block_toolbar">
        <a href="{$view_all_link}">{text %.event.view_all}</a>
    </div>
    {/if}
    {block}
    	{if $false_label}
        	
        {else}
			{component $profile_list1}
        {/if}
    {/block}
{/container}
