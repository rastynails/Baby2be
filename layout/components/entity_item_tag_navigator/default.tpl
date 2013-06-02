{* component Entity Item Tag Navigator *}

{container stylesheet="style.style"}
{block title=%tags}
	{if $no_tags}
	   {$no_tags}
	{else}
		{foreach from=$tags item='tag'}
	    	<a href="{$tag.url}">{$tag.label|censor:'tag':true}</a>&nbsp;&nbsp;
	    {/foreach}
    {/if}
{/block}
{/container}
