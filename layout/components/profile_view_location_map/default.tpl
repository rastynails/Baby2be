
{container}
    {block title=$smarty.capture.title}
	{block_cap title=%title}
		<a class="delete_cmp" href="javascript://"></a>{menu type="ajax-block" items=$music_menu_items}
	{/block_cap}
	

        {component ProfileLocationMap profile_id=$profile_id}
        {if isset($distance) && $distance >= 0 }
            {text %distance distance=$distance unit=$unit opponent_username=$opponent_username}
        {/if}
    {/block}
{/container}