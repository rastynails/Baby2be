{container}
<div {id="image-list-c"}>
    {foreach from=$images item='image'}
        <a href="javascript://" sk-image-id="{$image.id}" sk-image-src="{$image.src}" class="add_btn">{$image.label}</a>&nbsp;&nbsp;<a class="rm_btn" sk-image-id="{$image.id}" href="javascript://" style="color:red;font-weight:bold;">X</a><br />
    {/foreach}
</div>
{/container}