{* component Blog Add Form *}

{container}

{component $images}

<div class="float_half_left wider">
	{form BlogEditForm}
		{capture name='edit_form'}
			{text %.blogs.cap_label_edit_post}
		{/capture}
        {block_cap title=$smarty.capture.edit_form} {menu type="block" items=$menu_array}{/block_cap}
        <div class="block_toolbar"></div>
        {block}
            <table class="form" style="width:100%;margin:auto;">
                <tbody>
                    <tr>
                        <td class="label">{label %.components.blog_add.field_label_title for='blog_title'}</td>
                        <td class="value">{input name='blog_title'}</td>
                    </tr>
                    <tr>
                        <td class="label">{label %.components.blog_add.field_label_text for='blog_text'}</td>
                        <td class="value">{input name='blog_text'}</td>
                    </tr>
                    {if $mdr}
                    <tr>
                        <td class="label">{label %.components.blog_add.field_label_news for='is_news'}</td>
                        <td class="value">{input name='is_news'}</td>
                    </tr>
                    {/if}
                    <tr>
                        <td colspan="2" class="block_submit right">
						{button action=$form->active_action} {button action='blog_post_edit_save'}</td>
                    </tr>
                </tbody>
            </table>
        {/block}
        {*block label='Preview'*}
        {*/block*}
   {/form}
</div>

<div class="float_half_right narrower">   
   {component $tags_edit}
</div>
<br clear="all" />
{/container}
