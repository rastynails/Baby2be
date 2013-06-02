{* Index Blog List Component template *}

{container}
{capture name='toolbar'}
	{if $is_moderator}<a href="{document_url doc_key = 'manage_blog'}">{text %.blogs.label_add_news}</a> | {/if}<a href="{$news_url}">{text %.blogs.label_view_all}</a> 
{/capture}

{block}
	{if $no_posts}
		{block_cap title=%.blogs.index_news_cap}{/block_cap}
	{else}
		{block_cap title=%.blogs.index_news_cap toolbar=$smarty.capture.toolbar}{/block_cap}
	{/if}
	
	{if $no_posts}
    	<div class="no_content">{text %.blogs.label_no_posts}</div>
	{else}
	    <ul class="thumb_text_list">
	    {foreach from=$bp_list item='blog_entry'}
		<li class="item">
		    <div class="list_thumb"><a href="{$blog_entry.blog_post_url}">{profile_thumb profile_id=$blog_entry.dto->getProfile_id() size='60' href=false}</a></div>
		    <div class="list_block">
			<div class="list_info">
				<div class="item_title"><a href="{$blog_entry.blog_post_url}">{$blog_entry.title|censor:'blog':true}</a></div>
				{text %.txt.by} <a href="{$blog_entry.profile_url}">{$blog_entry.username}</a>, {$blog_entry.dto->getCreate_time_stamp()|spec_date}
			</div>
			<div class="list_content">{$blog_entry.desc|smile|out_format|censor:'blog'}</div>
		    </div>
		    <div class="clr"></div>
		</li>
	    {/foreach}
	    </ul>
	{/if}
{/block}
{/container}
