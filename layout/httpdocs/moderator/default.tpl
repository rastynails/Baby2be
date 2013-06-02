{* Index Blog List Component template *}

{canvas}

{block title=%.blogs.cap_label_posts_to_moderate}
	{if $no_posts}{text %.blogs.label_no_posts}{else}
    <ul class="thumb_text_list" {id="123"}>
    {foreach from=$bp_list item='blog_entry'}
        <li class="item">
            <div class="list_thumb"><a href="{$blog_entry.temp.blog_post_url}">{profile_thumb profile_id=$blog_entry.dto->getProfile_id() size='60' href=false}</a></div>
            <div class="list_block">
            	<div class="list_info">
					<div class="item_title"><a href="{$blog_entry.temp.blog_post_url}">{$blog_entry.temp.title|censor:'blog':true}</a></div>
                	{text %.txt.by} <a href="{$blog_entry.temp.profile_url}">{$blog_entry.username}</a>, {$blog_entry.dto->getCreate_time_stamp()|spec_date}            	
            	</div>
                <div class="list_content">{$blog_entry.temp.preview_text|censor:'blog'}</div>
            </div>
            <br clear="all" />
        </li>
    {/foreach}
    </ul>
    {/if}
{paging total=$bp_count on_page=10 pages=10 var='bPage'}
{/block}

{block title=%.event.cap_label_events_to_maderate}

{if $no_events}{text %.event.no_events}
{else}

	<ul class="thumb_text_list">
        {foreach from=$event_list item='event'}
        <li class="item">
            <div class="list_thumb"><a href="{$event.event_url}"><img class="thumb" align="left" src="{$event.image_url}" /></a></div>
            <div class="list_block">
                <div class="list_info">
                    <div class="item_title"><a href="{$event.event_url}">{$event.dto->getTitle()|censor:'event':true}</a></div>
                    {text %.event.by} <a href="{$event.profile_url}">{$event.username}</a> , {$event.dto->getStart_date()|spec_date}
                </div>
                <div class="list_content">{$event.dto->getDescription()|censor:'event'}</div>
                
            </div>
        </li>
        {/foreach}
    </ul>
    {paging total=$event_count on_page=10 pages=10 var='ePage'}

{/if}
{/block}

{if $to_approve}
    {block title=%.components.cls.approve_item}
		{if $no_cls}{text %.components.cls.no_item}
		{else}

	    <ul class="thumb_text_list" {id="1234"}>
	    {foreach from=$cls_list item='item_entry'}
	        <li class="item">
	            <div class="list_thumb">
	            	{cls_thumb thumb=$item_entry.item_thumb  title=$item_entry.title size='60' href=$item_entry.item_url}
	            </div>
	            <div class="list_block">
	            	<div class="list_info">
						<div class="item_title"><a href="{$item_entry.item_url}">{$item_entry.title|censor:'classifieds':true}</a></div>
	                	{text %.txt.by} <a href="{$item_entry.profile_url}">{$item_entry.username}</a>, {$item_entry.dto->getCreate_stamp()|spec_date}               	
	            	</div>
	                <div class="list_content">{$item_entry.description}</div>
	            </div>
	            <br clear="all" />
	        </li>
	    {/foreach}
	    </ul>
		
		{paging total=$cls_count on_page=10 pages=10 var='cPage'}
		{/if}
	{/block}    
{/if}
{/canvas}
