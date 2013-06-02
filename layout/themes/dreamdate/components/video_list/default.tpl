
{canvas}
{container stylesheet="video_list.style"}

<br />
{if $list_type eq 'tags' && !$tag_words}
    {component $VideoTagNavigator}
{else}

{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
{capture name='list_title'}
    {if $list_type eq 'tags'}{text %tags_label} '{$tag_words}'{elseif $list_type eq 'profile'}{text %video_by} {$username}{else}{text %list.`$list_type`}{/if}
{/capture}

<div class="float_half_left wider">

{block title=$smarty.capture.list_title}
    {if $list}
    <ul class="video_list">
    {foreach from=$list item='video'}
        <li class="list_item">
            <div class="video_info">
                <div class="small">
                    {rate rate=$video.rate_score feature='video'}
                    {text %by} <a href="{document_url doc_key='profile' profile_id=$video.profile_id}">{$video.username}</a>, 
                    <br />{$video.upload_stamp|spec_date}<br />
                    {text %views} <span class="highlight">{$video.view_count}</span> <br />
                    {text %comments} <span class="highlight">{$video.comment_count}</span> <br />
                    {if $enable_categories && $video.category_id}{text %category} <span class="highlight">{text %.video_categories.cat_`$video.category_id`}</span>{/if}
                </div>
            </div>
            <div class="video_thumb">
                {if $video.thumb_img eq 'default'}
                    <a href="{$video.video_page}"><div class="video_def_thumb"></div></a>
                {elseif $video.thumb_img eq 'friends_only'}
                    <div class="video_friends_thumb"></div>
                {elseif $video.thumb_img eq 'password_protected'}
                    <a href="{$video.video_page}"><div class="video_password_thumb"></div></a>
                {else}
                    <a href="{$video.video_page}">
                        <img src="{$video.thumb_img}" class="video_thumb" align="left" />
                    </a>
                    <div class="video_play_icon"></div>
                {/if}
            </div>
            <div class="video_body">
                {if $video.thumb_img != 'friends_only'}<a href="{$video.video_page}">{$video.title|out_format|smile|censor:"video":true}</a>
                {else}
                    <span class="a_fake">{$video.title|out_format|smile|censor:"video":true}</span>
                {/if}
                <p>{$video.description|out_format|truncate:150|smile|censor:"video":true}</p>
            </div>
            <br clear="all" />
            <div class="clr"></div>
        </li>
    {/foreach}
    </ul>
    {else}
       <div class="no_content">{text %.components.index_video.no_video}</div>
    {/if}
{/block}
</div>

<div class="float_half_right narrower">
    {component VideoCategories}
    
    {component $VideoTagNavigator}
</div>

<br clear="all" />
{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
{/if}
{/container}
{/canvas}