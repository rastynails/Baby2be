{* component Cls Index Item List *}

{container}

{capture name=toolbar}
{if $items_list.latest}
	<a href="javascript://" {id="view_more"}>{text %.components.cls.view_more}</a>
{/if}
{/capture}

{block}
{block_cap title=%.components.cls.`$entity`_listings toolbar=$smarty.capture.toolbar}
	<div class="menu">{menu type="block" items=$menu_items}</div>
{/block_cap}

<div class="item_list" {id='latest'}>
{if $items_list.latest}
<ul class="thumb_text_list">
	{foreach from=$items_list.latest item=item}
		<li class="item">
			<div class="list_thumb">
				{cls_thumb thumb=$item.item_thumb  title=$item.title size='60' href=$item.item_url}
			</div>
			<div class="list_block">
				<div class="list_info">
					<div class="item_title"><a href="{document_url doc_key='classifieds_item' item_id=$item.item_id}">{$item.title|escape|censor:'classifieds'}</a></div>
				</div>
				<div class="list_content small">
					{text %.components.cls.by} {profile_username profile_id=$item.profile_id}
					{$item.create_stamp|spec_date}. {if $item.end_stamp}<br />{text %.components.cls.ends} {$item.end_stamp|spec_date}.{/if}<br/>
					{text %.components.cls.`$entity`_money} {item_budget_price entity=$entity budget_min=$item.budget_min budget_max=$item.budget_max price=$item.price currency=$item.currency}
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

<div class="item_list" {id='ending_soon'}>
{if $items_list.ending_soon}
<ul class="thumb_text_list">
	{foreach from=$items_list.ending_soon item=item}
		<li class="item">
			<div class="list_thumb">
				{cls_thumb thumb=$item.item_thumb  title=$item.title size='60' href=$item.item_url}
			</div>
			<div class="list_block">
				<div class="list_info">
					<div class="item_title"><a href="{document_url doc_key='classifieds_item' item_id=$item.item_id}">{$item.title|escape|censor:'classifieds':true}</a></div>
				</div>
				<div class="list_content small">
					{text %.components.cls.by} {profile_username profile_id=$item.profile_id} 
					{$item.create_stamp|spec_date}. {if $item.end_stamp}<br />{text %.components.cls.ends} {$item.end_stamp|spec_date}{/if}.<br/>
					{text %.components.cls.`$entity`_money} {item_budget_price entity=$entity budget_min=$item.budget_min budget_max=$item.budget_max price=$item.price currency=$item.currency}
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

{/block}

{/container}