{container stylesheet="rss.style"}

    {capture name=rss}
        <ul class="rss_list">
        {foreach from=$rss item=item}
            <li>
                <div class="rss_header">
                  <a href="{$item.link}">{$item.title}</a>
                </div>
            
                {if $box.type == 'full'}
                    <div class="rss_content">
                      {$item.description}
                    </div>
                {/if}
                
                <div class="rss_footer small">
                   {if $item.time}
	                {$item.time|spec_date:true}
                   {/if}
                </div>
            </li>
        {foreachelse}
            <li class="no_content">
                {text %no_items}
            </li>
        {/foreach}
        </ul>
    {/capture}

    {if $box.block}
	    {block title=$box.title}
	        {$smarty.capture.rss}
	    {/block}
	{else}
	   {$smarty.capture.rss}
    {/if}
    
{/container}