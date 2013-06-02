{* Index Blog List Component template *}

{container}
{capture name='toolbar'}
	<a href="{$blog_url}">{text %.blogs.label_view_all}</a>
{/capture}

{if !$bp_list}
	{block_cap title=%.blogs.profile_page_cap_label}<a class="delete_cmp" href="javascript://"></a>{/block_cap}
{else}
	{block_cap title=%.blogs.profile_page_cap_label toolbar=$smarty.capture.toolbar}<a class="delete_cmp" href="javascript://"></a><br clear="all" />{/block_cap}
{/if}

{block}	
    {if !$bp_list}
     	<div class="no_content">{text %.blogs.label_no_posts}</div>
    {else}

	<ul class="text_list">
    {foreach from=$bp_list item='blog_entry'}
        <li class="item">
            <div class="list_block">
            	<div class="list_info">
	            	<div class="item_title"><a href="{$blog_entry.post_url}">{$blog_entry.title|censor:'blog':true}</a></div>
	            	{$blog_entry.dto->getCreate_time_stamp()|spec_date}
	            </div>
	            <div class="list_content">{$blog_entry.text|smile|censor:'blog'}</div>
            </div>
        </li>
    {/foreach}
    </ul>
    {/if}
{/block}
{/container}
