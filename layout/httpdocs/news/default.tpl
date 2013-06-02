{* Index Blog List Component template *}

{canvas}
<div>{paging total=$bp_count on_page=$on_page pages=10}</div>
<div class="float_half_left wider">
{block title=%.blogs.news_page_cap_label}
	{if $no_posts}<div class="no_content">{text %.blogs.label_no_posts}</div>{else}
    <ul class="thumb_text_list" {id="123"}>
    {foreach from=$bp_list item='blog_entry'}
        <li class="item">
            <div class="list_thumb"><a href="{$blog_entry.temp.blog_post_url}">{profile_thumb profile_id=$blog_entry.dto->getProfile_id() size='60' href=false}</a></div>
            <div class="list_block">
            	<div class="list_info">
					<div class="item_title"><a href="{$blog_entry.temp.blog_post_url}">{$blog_entry.temp.title}</a></div>
                	{text %.txt.by} <a href="{$blog_entry.temp.profile_url}">{$blog_entry.username}</a>, {$blog_entry.dto->getCreate_time_stamp()|spec_date}            	
            	</div>
                <div class="list_content">{$blog_entry.temp.preview_text|smile|out_format|censor:'blog'}</div>
            </div>
            <br clear="all" />
        </li>
    {/foreach}
    </ul>
    {/if}
{/block}

{paging total=$bp_count on_page=$on_page pages=10}
</div>
<div class="float_half_right narrower">

{component $tag_navigator}
</div>
{/canvas}
