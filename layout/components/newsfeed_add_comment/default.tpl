{* component Newsfeed Comment Add Form *}

{container stylesheet='add_comment.style'}
        <div {id='comment_list_cont'}>{component $comment_list}</div>
        
        {block expandable=yes expanded=true id="add_comment_form_block" class="newsfeed_add_comment_form"}
            {if $err_message}
                <div class="no_content">{$err_message}</div>
            {else}
            {form CommentAdd entity_id=$entity_id feature=$feature entityType=$entityType}
                <div class="newsfeed_formatter_wrap">{text_formatter for="comment_text" entity="comment"}</div>
                {input name='comment_text'}<br />
                <div class="block_submit">{button action='add_comment'}</div>
            {/form}
            {/if}
        {/block}
  
{/container}
