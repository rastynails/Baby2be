
{canvas}
{container}
    {block title=%categories}
    {foreach from=$categories item='cat' name='c'}
        <a href="{document_url doc_key='category_video' cat_id=$cat.category_id}">{if $cat.category_id == $active}<b>{$cat.label}</b>{else}{$cat.label}{/if}</a> 
        ({$cat.videoCount}){if !$smarty.foreach.c.last},<br />{/if}
    {/foreach}
    {/block}
{/container}
{/canvas}