{* component Blog Manage List *}

{container stylesheet='style.style'}
	{capture name='manage_list'}
		{text %.components.blog_manage_list.main_cap_label}
	{/capture}
	
	{form BlogAddForm}
        {block_cap title=$smarty.capture.manage_list} {menu type="block" items=$menu_array}{/block_cap}
        <div class="block_toolbar"></div>
        {block}
            <table class="form">
                <thead>
                    <tr>
                        <th style="width:55%;">{text %.components.blog_manage_list.table_head_post}</th>
                        <th>{text %.components.blog_manage_list.table_head_comments}</th>
                        <th>{text %.components.blog_manage_list.table_head_views}</th>
                        <th>{text %.components.blog_manage_list.table_head_date}</th>
                        <th>{text %.components.blog_manage_list.table_head_action}</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$blog_list item='post'}
                    <tr class="list_item">
                        <td><a href="{$post.temp.post_url}">{$post.dto->getTitle()}</a></td>
                        <td class="post_count center">{$post.items_count}</td>
                        <td class="post_count center">{if !$post.dto->getView_count()}0{else}{$post.dto->getView_count()}{/if}</td>
                        <td class="blog_date small">{$post.dto->getCreate_time_stamp()|spec_date}</td>
                        <td class="blog_actions"><a href="{$post.temp.edit_url}" >{text %.components.blog_manage_list.post_edit}</a> &#183; <a href="javascript://" onclick="SK_confirm($('<div>{text %.label.confirm_cap_label}</div>'),$('<div>{text %.msg.delete_blog_post_confirm}</div>'), function(){literal}{{/literal} window.location='{$post.temp.delete_url}'; {literal}}{/literal});">{text %.components.blog_manage_list.post_delete}</a></td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            {paging total=$total on_page=$on_page pages=$pages}
        {/block}
    {/form}
{/container}
