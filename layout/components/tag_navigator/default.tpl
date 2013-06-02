{* component Comment Add Form *}

{container}
	
	{capture name='tag_navigator'}
		{text %.components.tag_navigator.cap_label}
	{/capture}
	
	{block title=$smarty.capture.tag_navigator}
		{if $no_tags}<div class="no_content">{text %no_tags}</div>
        {else}
		{foreach from=$tags item='tag'}
	    	<a href="{$tag.url}" style="font-size:{$tag.size}px;line-height:{$tag.line_height}px;">{$tag.tag_label|censor:"tag"}</a>&nbsp;&nbsp;
	    {/foreach}
        {/if}
	{/block}
	
{/container}
