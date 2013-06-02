
{container}

{capture name="title"}
	{text %title username=$username}
{/capture}

{capture name=toolbar}
	{if count($groups)}<a href="{document_url doc_key='groups' member_id=$profile_id}">{text %view_all}</a>{/if}
{/capture}

	{block}
		{block_cap title=$smarty.capture.title toolbar=$smarty.capture.toolbar}
			<a class="delete_cmp" href="javascript://"></a>
		{/block_cap}
		{if count($groups)}
		{foreach from=$groups item='group'}
			<a href="{document_url doc_key='group' group_id=$group.group_id}">
				<img src="{$group.thumb}" title="{$group.title|censor:'group':true}" width=75 />
			</a>
		{/foreach}
		{else}
			<div class="no_content">{text %no_groups}</div>
		{/if}
	{/block}

{/container}