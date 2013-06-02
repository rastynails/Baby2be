{container class="simple_friend_list.style" class="simple_friend_list"}

{capture name="title"}
{text %title username=$username}
{/capture}

{capture name=toolbar}{if $view_all}<a href="{document_url doc_key='profile_friend_list' profile_id=$profile_id}">{text %view_more}</a>{/if}{/capture}

{block}
	{block_cap title=$smarty.capture.title toolbar=$smarty.capture.toolbar}
	<a class="delete_cmp" href="javascript://"></a>
	{/block_cap}
	<div class="container">
	{if count($list)}
		{component SimpleList items=$list}
	{else}
		<div class="no_content">
			{text %no_items}
		</div>
	{/if}
	</div>
	
{/block}

{/container}