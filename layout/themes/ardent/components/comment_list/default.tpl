{* component Comment Add Form *}

{container stylesheet='comment_list.style'}
{if $comments}
{block title=%.comment.comments_cap_label}
<div {id="comment_list_cont"}>
	<ul class="thumb_text_list">
    	{foreach from=$comments item='comment_item'}
        <li class="item">
            <div class="list_thumb">
            	{profile_thumb profile_id=$comment_item.dto->getAuthor_id() size='40'}
            </div>
            <div class="list_block">
            	<div class="list_info">
            		<div class="comment_stat">
	                    <div class="item_title">
                        	{if $comment_item.username}
                        		<a href="{$comment_item.profile_url}">{$comment_item.username}</a>
	                        {else}
                            	<span>{text %.label.deleted_member}</span>
    	                    {/if}
                         </div>
						{$comment_item.dto->getCreate_time_stamp()|spec_date}
	                </div>
	                <div class="del_link">
    	            	{if $comment_item.delete}<a href="javascript://" {id="`$comment_item.delete_link_id`"}>{text %.comment.comment_manage_delete}</a>{/if}
        	        </div>
				</div>
                <div class="list_content">
            		{$comment_item.dto->getText()|out_format:'comment'|censor:'comment'|smile}
            	</div
            ></div>
        </li>
        {/foreach}        
 	</ul>
 	{if $pages}
      	<div class="paging">
        	<span>Pages: </span>
			{foreach from=$pages item='page'}
				<a{if $page.active} class="active"{/if} href="javascript://" {id="`$page.id`"}>{$page.label}</a>
			{/foreach}
		</div>
      {/if}
 </div>
{/block}
{/if}
{/container}
