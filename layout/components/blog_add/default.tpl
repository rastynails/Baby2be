{* component Blog Add Form *}

{container}
	{capture name='add_blog'}
		{text %.components.blog_add.cap_label}
	{/capture}
    
    {component $images}
    
	{form BlogAddForm}
        {block_cap title=$smarty.capture.add_blog} {menu type="block" items=$menu_array}{/block_cap}
        {block}
        
        {if $err_message}
        	<div class="no_content">{$err_message}</div>
        {else}
            <div class="wide automargin" style="width: 100%">

            <table class="form">
                <tbody>
                    <tr>
                        <td class="label">{label %.components.blog_add.field_label_title for='blog_title'}</td>
                        <td class="value all_row_width">{input name='blog_title'}</td>
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
                        <td colspan="2" class="block_submit right">{button action='blog_post_draft'} {button action='blog_post_publish'}</td>
                    </tr>
                </tbody>
            </table>
         </div>
        {/if}  
        {/block}
    {/form}
{/container}
