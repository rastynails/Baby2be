
{container stylesheet="index_group_list.style"}
	{block_cap title=%title}
		{menu type="ajax-block" items=$groups_menu_items}
	{/block_cap}
	{block}
	{if $no_groups}
		<div class="no_content">{text %no_groups}</div>
	{else}
	<div {id="groups_cont"}>
		{foreach from=$groups item='list' key='list_name'}

		<div class="group_list" {id="group_`$list_name`"} style="display: {if $list.is_active}block;{else}none;{/if}">
			<div class="block_toolbar"><a href="{document_url doc_key='groups'}">{text %view_all}</a></div>
			<ul class="thumb_text_list">
				{foreach from=$list.groups item='group'}
					<li class="item">
						<div class="list_thumb">
							<a href="{document_url doc_key='group' group_id=$group.group_id}"><img title="{$group.title|censor:'group':true}" src="{$group.img}" width="90" /></a>
						</div>
						<div class="list_block" style="overflow: hidden">
							<div class="list_info">
								<div class="item_title"><a href="{document_url doc_key='group' group_id=$group.group_id}">{$group.title|truncate:60|censor:'group':true}</a></div>
								{text %by} {$group.username}, {$group.creation_stamp|spec_date}
							</div>
							<div class="list_content">{$group.description|smile|out_format|truncate:160|censor:'group'}</div>
						</div>
						<div class="clr" />
					</li>
				{/foreach}
			</ul>
		</div>
		{/foreach}
	</div>
	{/if}
	
	{/block}
{/container}