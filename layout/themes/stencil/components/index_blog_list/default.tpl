{* Index Blog List Component template *}

{container}


{block}
{block_cap title=%.components.profile_preference.configs.blogs.title|censor:'blog':true}
{menu type="ajax-block" items=$menu_array}
{/block_cap}
    <div {id="latest_cont"}>
        {if $no_posts}
            <div class="no_content">{text %.blogs.label_no_posts}</div>
        {else}
        <ul class="thumb_text_list">
        {foreach from=$bp_list item='blog_entry'}
            <li class="item clearfix">
                <div class="list_thumb"><a href="{$blog_entry.blog_post_url}">{profile_thumb profile_id=$blog_entry.dto->getProfile_id() size='60' href=false}</a></div>
                <div class="list_block">
                    <div class="list_info">
                        <div class="item_title"><a href="{$blog_entry.blog_post_url}">{$blog_entry.title|censor:'blog':true}</a></div>
                        {text %.txt.by} <a href="{$blog_entry.profile_url}">{$blog_entry.username}</a>, {$blog_entry.dto->getCreate_time_stamp()|spec_date}
                    </div>
                    <div class="list_content">{$blog_entry.desc|censor:'blog'|smile|out_format:'blog':false}</div>
                </div>
            </li>
        {/foreach}
	 <li class="clr_div"></li>
        </ul>
        {/if}
    </div>



    <div {id="popular_cont"} style="display:none;">
    	{if $no_posts}
            <div class="no_content">{text %.blogs.label_no_posts}</div>
        {else}
        <ul class="thumb_text_list">
        {foreach from=$pop_bp_list item='blog_entry'}
            <li class="item clearfix">
                <div class="list_thumb"><a href="{$blog_entry.blog_post_url}">{profile_thumb profile_id=$blog_entry.dto->getProfile_id() size='60' href=false}</a></div>
                <div class="list_block">
                    <div class="list_info">
                        <div class="item_title"><a href="{$blog_entry.blog_post_url}">{$blog_entry.title|censor:'blog':true}</a></div>
                        {text %.txt.by} <a href="{$blog_entry.profile_url}">{$blog_entry.username}</a>, {$blog_entry.dto->getCreate_time_stamp()|spec_date}
                    </div>
                    <div class="list_content">{$blog_entry.desc|censor:'blog'|smile|out_format:'blog':false}</div>
                </div>
            </li>
        {/foreach}
        </ul>
        {/if}
    </div>
    <div class="index_blog_btn"><a href="{$blogs_url}"><span>{text %.blogs.label_view_all}</span></a></div>

{/block}


{/container}
