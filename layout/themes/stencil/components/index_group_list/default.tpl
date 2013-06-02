{container stylesheet="index_group_list.style"}
	{block}
	{block_cap title=%title}
		{menu type="ajax-block" items=$groups_menu_items}
	{/block_cap}
	{if $no_groups}
		<div class="no_content">{text %no_groups}</div>
	{else}
	<div {id="groups_cont"}>
		{foreach from=$groups item='list' key='list_name'}

		<div class="group_list" {id="group_`$list_name`"} style="display: {if $list.is_active}block;{else}none;{/if}">
			<ul class="thumb_text_list">
				{foreach from=$list.groups item='group'}
					<li class="item clearfix">
						<div class="list_thumb">
							<a class="index_group_img" href="{document_url doc_key='group' group_id=$group.group_id}"><img title="{$group.title|censor:'group':true}" src="{$group.img}" width="90" /></a>
							<div class="item_title"><a href="{document_url doc_key='group' group_id=$group.group_id}">{$group.title|truncate:60|censor:'group':true}</a></div>
						</div>
						<div class="list_block" style="overflow: hidden">
							<div class="list_info">
								{text %by} <strong>{$group.username}</strong>, {$group.creation_stamp|spec_date}
							</div>
							<div class="list_content">{$group.description|smile|out_format|truncate:160}</div>						
						</div>
					</li>
				{/foreach}
			</ul>

			<div class="index_group_btn"><a href="{document_url doc_key='groups'}"><span>{text %view_all}</span></a></div>
		</div>
		{/foreach}
	</div>
	{/if}
	
	{/block}
{/container}