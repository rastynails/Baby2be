{* Httpdoc View Blog *}

{canvas}
<div style="width:66%;float:left;">
	{foreach from=$bp_list item='blog_post'}
    	{block_cap}<h3 style="height:18px;overflow:hidden;">{$blog_post.cap_label}</h3>{/block_cap}
    	{block}
        <div style="float:left;width:120px;margin-right:-120px;">
        	<a href="{$blog_post.url}">{profile_thumb profile_id=$blog_post.dto->getProfile_id() username=true size='60' href=false}</a>
        </div>
        <div style="margin-left:120px;padding-left:7px;">{$blog_post.text|censor:'blog'}</div>
        <br clear="all" />
        <div style="text-align:right;">
        	<a href="{$blog_post.url}">{text %.blogs.link_label_read_more}</a> | <a href="{$blog_post.url}#comments">{if $blog_post.comments_count}{$blog_post.comments_count}{else}0{/if} {text %.blogs.label_comment_s}</a>
        </div>
        {/block}
    {/foreach}
	<br />
	{paging total=$bp_count on_page=$on_page pages=10}
</div>
<div style="float:right;width:33%;">
	{component $tags}
</div>

{/canvas}