{* component Newsfeed *}

{container stylesheet="newsfeed.style"}
    {block_cap title=%cap_label}<a class="delete_cmp" href="javascript://"></a>{/block_cap}
	{block}

    <div class="center">
        {$no_permission_msg}
    </div>
        
	{/block}
{/container}