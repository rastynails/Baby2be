
{canvas}
	{if $err_message}
    	<div class="no_content">{$err_message}</div>
    {else}
	{capture name='post_title'}
		{$blog_post->getTitle()|censor:'blog':true}
	{/capture}
    {block title=$smarty.capture.post_title}
        <div class="time small right">{$blog_post->getCreate_time_stamp()|spec_date}</div>
        <div>
        	<div style="float:left;margin-right:5px;margin-bottom:5px;">{profile_thumb profile_id=$blog_post->getProfile_id() username=true size='60'}</div>
            {$blog_post_text|censor:'blog'|smile|out_format:'blog'}
            <br clear="all" />
            <div>{paging total=$blog_post_pages_count on_page=1 pages=10}</div>
        </div>
        
        <div style="text-align:right">
            <div style="padding-bottom:4px;">{component Report type='blog' reporter_id=$reporter_id entity_id=$blog_post->getId()}</div>
            {if $edit_url}<input type="button" {id="edit"} value="{text %.blogs.label_edit}" onclick="window.location='{$edit_url}'" /> {/if}
            {if $approve_button}<input type="button" {id="approve"} value="{text %.blogs.label_approve}" /> {/if}
            {if $block_button}<input type="button" {id="approve"} value="{text %.label.moder_block}" /> {/if}
            {if $approve_button || $block_button}<input type="button" {id="delete"} value="{text %.label.moder_delete}" />{/if}
        </div>
       {if empty($isNews)}
       <div style="border-top:1px dashed #ccc;border-bottom:1px dashed #ccc;margin-top:10px;padding:5px;">
            <div style="float:left;width:40%;">{if $prev_url}<a href="{$prev_url}">{text %.blogs.label_prev_post}</a>{/if}</div>
            <div style="float:right;width:40%;text-align:right;">{if $next_url}<a href="{$next_url}">{text %.blogs.label_next_post}</a>{/if}</div>
            <br clear="all" />
       </div>
        {/if}
    {/block}
    <div style="width:66%;float:left;">{component ContentSocialSharing}{component $comments}</div>
    <div style="float:right;width:33%;">{if empty($isNews)}{component $rate}{/if}{component $en_tag_navigator}</div>
    <br clear="all" />
    {/if}
{/canvas}