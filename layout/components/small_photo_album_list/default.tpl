{* Component Album List *}

{container stylesheet="profile_photo_album_list.style"}
	
	{capture name=toolbar}{if $show_all_btn}<a href="{document_url doc_key='photo_albums' profile_id=$profile_id}">{text %view_all}</a>{/if}{/capture}
	
	{block}
		{block_cap title=$title toolbar=$smarty.capture.toolbar}
			<a class="delete_cmp" href="javascript://"></a>
		{/block_cap}
		<div class="list_cont">
			<ul>
			{foreach from=$items item=item}
				<li class="item">
					<a href="{$item->getUrl(false)}">
						<img src="{$item->getThumb_url('mini')}" width="60" height="60"/>
					</a>
					<a href="{$item->getUrl(false)}">
						{$item->getView_label()}
					</a>
				</li>
			{foreachelse}
				<li class="no_content">
					{text %no_items}
				</li>
			{/foreach}
			<li class="clr"></li>
			</ul>
		</div>
	{/block}
{/container}