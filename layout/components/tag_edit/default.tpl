{* component Comment Add Form *}

{container}

{block title=%.components.tag_edit.cap_label_manage_tags}
    {form TagAdd}
    	<div class="center">
        	{input name='tags'}<br />
        <div class="block_submit">{button action='add_tag'}</div>
        </div>
    {/form}
    <div {id="tag_list"}>{component $tags_cmp}</div>
{/block}
{/container}
