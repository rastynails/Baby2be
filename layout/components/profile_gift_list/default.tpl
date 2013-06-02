
{canvas}

{container stylesheet="profile_gift_list.style"}

{block}
    {capture assign='toolbar'}
        {if $gifts}<a href="{document_url doc_key='gifts' profile_id=$profile_id}">{text %.gifts.view_all}</a>{else}&nbsp;{/if}
    {/capture}

    {if $owner_mode}
        {capture assign='title'}{text %.gifts.your_gifts count=$gift_count}{/capture}
        {block_cap title=$title|censor:'event':true toolbar=$toolbar}<a class="delete_cmp" href="javascript://"></a>{/block_cap}
    {else}
        {capture assign='title'}{text %.gifts.profile_gifts username=$username count=$gift_count}{/capture}
        {block_cap title=$title|censor:'event':true toolbar=$toolbar}{/block_cap}
    {/if}
    
    {if !$gifts}
        <div class="no_content">{text %.gifts.no_gifts}</div>
    {else}
    
    <div class="gift_list list_cont">
        <ul>
            {foreach from=$gifts item='gift'}
            <li class="item" title="{text %.gifts.gift_by username=$gift.username}">
                <a href="{document_url doc_key='gift' gid=$gift.gift_id}"><img src="{$gift.picture}" width="80" /></a><br />
                <a href="{document_url doc_key='gift' gid=$gift.gift_id}"><span class="small">{$gift.sign_text|truncate:9:"..."|censor:'event':true}</span></a>
            </li>
            {/foreach}
            <li class="clr"></li>
        </ul>
    </div>
    
    {/if}
    
{/block}

{/container}

{/canvas}