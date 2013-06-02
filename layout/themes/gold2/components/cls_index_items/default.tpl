{* component Cls Index Items *}

{container}

{block}
{block_cap title=%label}
	<div class="menu">{menu type="block" items=$menu_items}</div>
{/block_cap}

<div class="item_list" {id='wanted'}>
{if $items_list.wanted}
<ul class="thumb_text_list">
	{foreach from=$items_list.wanted item=item}
		<li class="item">
			<div class="list_thumb">
				{cls_thumb thumb=$item.item_thumb  title=$item.title|censor:'classifides':true size='60' href=$item.item_url}
			</div>
			<div class="list_block">
				<div class="list_info">
					<div class="item_title"><a href="{document_url doc_key='classifieds_item' item_id=$item.item_id}">{$item.title|censor:'classifides':true}</a></div>
				</div>
				<div class="list_content small">
					{text %.components.cls.by} {profile_username profile_id=$item.profile_id}
					{$item.create_stamp|spec_date}. {if $item.end_stamp}{text %.components.cls.ends} {$item.end_stamp|spec_date}.{/if}<br/>
					{text %.components.cls.wanted_money} {item_budget_price entity='wanted' budget_min=$item.budget_min budget_max=$item.budget_max price=$item.price currency=$item.currency}
				</div>
			</div>
			<div class="clr"></div>
		</li>
	{/foreach}
</ul>
{else}
	<div class="no_content">{text %.components.cls.no_item}</div>
{/if}
</div>

<div class="item_list" {id='offer'}>
{if $items_list.offer}
<ul class="thumb_text_list">
	{foreach from=$items_list.offer item=item}
		<li class="item">
			<div class="list_thumb">
				{cls_thumb thumb=$item.item_thumb  title=$item.title size='60' href=$item.item_url}
			</div>
			<div class="list_block">
				<div class="list_info">
					<div class="item_title"><a href="{document_url doc_key='classifieds_item' item_id=$item.item_id}">{$item.title}</a></div>
				</div>
				<div class="list_content small">
					{text %.components.cls.by} {profile_username profile_id=$item.profile_id} 
					{$item.create_stamp|spec_date}. {if $item.end_stamp}{text %.components.cls.ends} {$item.end_stamp|spec_date}{/if}.<br/>
					{text %.components.cls.offer_money} {item_budget_price entity='offer' budget_min=$item.budget_min budget_max=$item.budget_max price=$item.price currency=$item.currency}
				</div>
			</div>
			<div class="clr"></div>
		</li>
	{/foreach}
</ul>
{else}
	<div class="no_content">{text %.components.cls.no_item}</div>
{/if}
</div>
<div class="clearfix"><div class="block_toolbar">
	{if $items_list.wanted}
		<a href="javascript://" {id="view_more"}>{text %.components.cls.view_more}</a>
	{/if}
</div></div>
{/block}

{/container}