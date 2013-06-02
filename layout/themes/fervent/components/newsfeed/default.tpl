{* component Newsfeed *}

{container stylesheet="newsfeed.style"}
    {block_cap title=%cap_label}<a class="delete_cmp" href="javascript://"></a>{/block_cap}
	{block}

	<ul class="newsfeed smallmargin">
	    {component $feed_list}
	</ul>

	{if $viewMore}
		<div class="newsfeed_view_more_c">
		    <input type="button" class="newsfeed_view_more" value="{text %view_more}">
		</div>
	{/if}
    
    <div style="display: none;">
        <div class="newsfeed_likes_userlist_cap">{text %likes_userlist_cap}</div>
        <div class="newsfeed_likes_userlist clearfix">
    </div>
        
    </div>

	{/block}
{/container}