<div class="clearfix"><div class="newsfeed_item_picture">
        <a href="{document_url doc_key=group group_id=$item.content.group_id}">
            <img src="{$item.content.img_src}" />
        </a>
    </div>
    <div class="newsfeed_item_content">
        <a class="newsfeed_item_title" href="{document_url doc_key=group group_id=$item.content.group_id}">{$item.content.title|out_format|censor:'group':true}</a>
        <div class="remark smallmargin">{$item.content.text|out_format|censor:'group'}</div>
    </div>
</div>