{if $item.content.img_src}
<div class="list_thumb">
    <a href="{$item.content.href}">
        <img title="{$item.content.title|out_format|censor:'comment':true}" class="profile_thumb" src="{$item.content.img_src}" width="60">
    </a>
</div>
{/if}
<div {if $item.content.img_src}class="list_block"{/if}>
    <div class="list_info">
        <div class="item_title"><a href="{$item.content.href}">{$item.content.title|out_format|censor:'comment':true}</a><br /></div>
    </div>
    <div class="list_content small">
        <div class="remark" style="paddig-top: 4px">{$item.content.text|out_format|censor:'comment'}</div>
    </div>
</div>
<div class="clr"></div>

