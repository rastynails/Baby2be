

{container}

{block}
	{capture assign='toolbar'}
		{if $events}<a href="{document_url doc_key='events' }">{text %.event.view_all}</a>{else}&nbsp;{/if}			        
	{/capture}
	{block_cap title=%.event.events toolbar=$toolbar}<a class="delete_cmp" href="javascript://"></a>{/block_cap}

{if !$events}
<div class="no_content">{text %.event.no_events}</div>
{else}
<ul class="thumb_text_list">
    {foreach from=$events item='event'}
    <li class="item">
        <div class="list_thumb"><a href="{$event.url}"><img class="thumb" align="left" src="{$event.image_url}" /></a></div>
        <div class="list_block">
        	<div class="list_info">
        		<div class="item_title"><a href="{$event.url}">{$event.title|censor:'event':true}</a></div>
        		{$event.date|spec_date}
        	</div>
        	<div class="list_content">{$event.desc|censor:'event'}</div>
        </div>
    </li>
    {/foreach}
</ul>
{/if}
{/block}
{/container}

