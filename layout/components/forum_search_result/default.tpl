{* component Forum Search Result *}
{canvas}
{container stylesheet="forum_search_result.style"}

	<div class="action_buttons">
	    {component ForumSearch}
    </div>
    <br />
	{block title="%search_result"}
		{if $result.total}
		{foreach from=$result.posts item=post}
		<table width="100%" class="post_list">
			<tr {id=post_`$post.forum_post_id`}>
				<td class="forum_thumb">{profile_thumb profile_id=$post.profile_id size=60}
                <p class="small">
                    <p class="small">
                        {if !$post.is_deleted}<a href="{document_url doc_key='profile_view' profile_id=$post.profile_id}">{$post.username}</a>
                        {else}{text %.label.deleted_member}{/if}
                    </p>
                </p>
                </td>
				<td class="list_item">
					<div class="right small">{$post.create_stamp|spec_date}</div>
					<div class="post_text">{$post.text|out_format:"forum"|smile|censor:'forum'}</div>
					<div class="search_result_bread">
						<a href="{document_url doc_key='forum_group_list'}">{$post.forum_group_name}</a> &raquo;
						<a href="{document_url doc_key='forum' forum_id=$post.forum_id}">{$post.forum_name|censor:'forum'}</a> &raquo;
						<a href="{$post.post_url}">{$post.title|out_format:"forum"|smile|censor:'forum':true}</a>
					</div>
				</td>
			</tr>
		</table>	
		{/foreach}
		{else}
			<div class="no_content">{text %search_nothing_found}</div>
		{/if}		
	{/block}
	<br/>
	{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}

{/container}
{/canvas}