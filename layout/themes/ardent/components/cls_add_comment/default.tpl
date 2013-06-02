{* component Cls Comment Add Form *}

{container}
     <a name="comments"></a>
             
     <div {id='comment_list_cont'}>{component $comment_list}</div>

     {block title=%.comment.cap_add_comment_label}
     <p class="center">
     {if $comment_error_msg}
     	<div class="no_content">{$comment_error_msg}</div>
     {else}
     {form ClsAddComment entity_id=$entity_id}
     	{text_formatter for="comment_text" entity="classifieds"}
         {input name='comment_text'}
         <span style="display:none;">{label for='comment_text'}</span>
         {if $bid_error_msg}
         	{$bid_error_msg}
         {else}
         	{text %your_bid} {input name='bid'}
         {/if}
         <div class="block_submit">{button action='add_comment'}</div>
     {/form}
     {/if}
     </p>
     {/block}
    
{/container}
