{* component Comment Add Form *}

{container}
<div {id="tag_list"}>
{foreach from=$tags item='tag'}
<a href="javascript://" {id=$tag.link_id} class="delete_comment" style="font-weight:bold;color:red;" title="Delete">X</a> {$tag.label|censor:'tag'}<br />
{/foreach}
</div>
{/container}
