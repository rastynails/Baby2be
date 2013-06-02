{container}
    {capture name=title}
        {text %title}
    {/capture}
	{block title=$smarty.capture.title}
        {component OnlineListLocationMap}
    {/block}
{/container}