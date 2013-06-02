{* component Cls Item List *}

{canvas}
{container stylesheet="cls_item_list.style"}

{block class="item_list"}
{block_cap title=%.components.cls.browse_item}
	<div class="menu">{menu type="block" items=$menu_items}</div>
{/block_cap}

{if $item_list}
	<table width="100%">
	<tbody>
	{foreach from=$item_list item=item}
	<tr>
		<td class="thumb">
			{cls_thumb thumb=$item.item_thumb  title=$item.title size='60' href=$item.item_url}
		</td>
		<td class="list_item">
			<a href="{document_url doc_key='classifieds_item' item_id=$item.item_id}">{$item.title}</a><br/>
			{$item.description|truncate:240|censor:'classifieds'}
		</td>
		<td width="150" style="padding-left: 10px;" class="small">
			{text %.components.cls.posted_by} {profile_username profile_id=$item.profile_id}<br/>
			{text %.components.cls.date}: {$item.create_stamp|spec_date}<br/>
			{if $item.end_stamp}{text %.components.cls.ends} {$item.end_stamp|spec_date}<br/>{/if}
			{text %.components.cls.`$item.entity`_money} 
			{item_budget_price entity=$item.entity budget_min=$item.budget_min budget_max=$item.budget_max price=$item.price currency=$item.currency}
			{if $item.last_bid}<br/>{text %.components.cls.latest_bid} {$item.currency}<b>{$item.last_bid}</b>{/if}
		</td>
	</tr>
	{/foreach}
	</tbody>
	</table>
	{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
{else}
	<div class="no_content">{text %.components.cls.no_item}</div>
{/if}
{/block}

{/container}
{/canvas}