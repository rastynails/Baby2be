
{canvas}

{container stylesheet="profile_all_gift_list.style"}

{block}
    {if $owner_mode}
        {capture assign='title'}{text %.gifts.your_gifts count=$gift_count}{/capture}
        {block_cap title=$title|censor:mailbox:true}{/block_cap}
    {else}
        {capture assign='title'}{text %.gifts.profile_gifts username=$username count=$gift_count}{/capture}
        {block_cap title=$title|censor:mailbox:true}{/block_cap}
    {/if}
    
    {if !$gifts}
        <div class="no_content">{text %.gifts.no_gifts}</div>
    {else}
    
    <div class="gift_list list_cont">
        <ul>
            {foreach from=$gifts item='gift'}
            <li class="item" title="{text %.gifts.gift_by username=$gift.username}">
                <a href="{document_url doc_key='gift' gid=$gift.gift_id}"><img src="{$gift.picture}" width="80" /></a><br />
                <a href="{document_url doc_key='gift' gid=$gift.gift_id}"><span class="small">{$gift.sign_text|truncate:30|censor:mailbox}</span></a>
            </li>
            {/foreach}
            <li class="clr"></li>
        </ul>
    </div>
    
    {paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
    
    {/if}
    
{/block}

{/container}

{/canvas}