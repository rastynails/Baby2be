{* component Comment Add Form *}

{container stylesheet='add_comment.style'}
        <a name="comments"></a>
        
        {if !$mode}
        {block title=%.comment.cap_add_comment_label}
        <p class="center">
        {if $err_message}
        	<div class="no_content">{$err_message}</div>
        {else}
        {form CommentAdd entity_id=$entity_id feature=$feature entityType=$entityType}
        	{text_formatter for="comment_text" entity="comment"}
        	<div style="display: none">{label %.comment.cap_add_comment_label for=comment_text}</div>
            {input name='comment_text'}<br />
            <div class="block_submit">{button action='add_comment'}</div>
        {/form}
 		{/if}
        </p>
        {/block}
        {/if}
        
        <div {id='comment_list_cont'}>{component $comment_list}</div>
  
        {if $mode}
        {block title=%.comment.cap_add_comment_label}
        <p class="center">
        {if $err_message}
        	<div class="no_content">{$err_message}</div>
        {else}
        {form CommentAdd entity_id=$entity_id feature=$feature}
        	{text_formatter for="comment_text" entity="comment"}
            <div style="display: none">{label %.comment.cap_add_comment_label for=comment_text}</div>
            {input name='comment_text'}<br />
            <div class="block_submit">{button action='add_comment'}</div>
        {/form}
        {/if}
        </p>
        {/block}
        {/if}
    
{/container}
