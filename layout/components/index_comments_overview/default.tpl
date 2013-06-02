{* Index Profile Comments Overview template *}

{container stylesheet='comment_list.style'}
{capture name='toolbar'}
	<a href="{$comments_url}">{text %label_view_all}</a>
{/capture}

{if !$comments}
	{block_cap title=%index_comments_overview_cap}{/block_cap}
{else}
	{block_cap title=%index_comments_overview_cap toolbar=$smarty.capture.toolbar}{/block_cap}
{/if}

{block}
<div class="feature_comment_list">

{if $comments}
    <div {id="comment_list_cont"}>
        <ul class="thumb_text_list">
        {foreach from=$comments item='comment_item'}
            <li class="item comment_list" id="{$comment_item.feature}_{$comment_item.id}">
                <div class="list_thumb">
                    {profile_thumb profile_id=$comment_item.author_id size='40'}
                </div>
                <div class="list_block">
                    <div class="list_info">
                        <div class="comment_stat">
                            <div class="item_title">
                                {if !$comment_item.is_deleted}
                                    <a href="{$comment_item.profile_url}">{$comment_item.username}</a>&nbsp;{text %.comment.commented}
                                   {if $comment_item.entity_url}
                                        <a href="{$comment_item.entity_url}">{$comment_item.entity_title|censor:'comment':true}</a>
                                   {else}
                                       {$comment_item.entity_title}
                                   {/if}
                                {else}
                                    <span>{text %.label.deleted_member}</span>
                                {/if}
                             </div>
                            {$comment_item.create_time_stamp|spec_date}
                        </div>
                        <div class="del_link">
                            {if $comment_item.delete}<a href="javascript://" {id="`$comment_item.delete_link_id`"}>{text %.comment.comment_manage_delete}</a>{/if}
                        </div>
                    </div>
                    <div class="list_content">
                        {$comment_item.text|out_format:'comment'|censor:'comment'|smile}
                    </div>
                </div>
            </li>
        {/foreach}
        </ul>
     </div>
{else}
    <div class="no_content">{text %.comment.no_items}</div>
{/if}
</div>
{/block}
{/container}

