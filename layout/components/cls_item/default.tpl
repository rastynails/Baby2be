{* component Cls Item *}

{canvas}
{container stylesheet='cls_item.style'}

<div class="float_half_left item_view">
{capture name=toolbar}
	{component Report type='classifieds' reporter_id=$profile_id entity_id=$item_info.item_id show_link=yes}
	{if $profile_id == $item_info.profile_id || $moderator}
	<div class="action_links">	
		{if $moderator && $to_approve}
		<a href="javascript://"  {id="approve_item"}>{text %approve_item}</a> | 
		{/if}
		<a href="{document_url doc_key='classifieds_edit_item' item_id=$item_info.item_id}">{text %edit_item}</a> |
		<a href="javascript://" {id="delete_item"}>{text %delete_item}</a>{if $profile_id != $item_info.profile_id && $moderator}&nbsp;|&nbsp;{/if}
	</div>	 
	{/if}
{/capture}
{block title=%item_title toolbar=$smarty.capture.toolbar}
	<div>{$item_info.description}</div>
	<div class="center footer_menu">
	{foreach from=$item_files item=file}
		<img {id=$file.file_id} class="item_files" src="{$file.file_url}" height="100"/>
	{/foreach}
	</div>
{/block}
{if $allow_comments}
{component $item_comments}
{/if}
<br/>
</div>
<div class="float_half_right item_info">
	{component ClsItemInfo item_info=$item_info}
	
    <div{if !$allow_bids} style="display: none"{/if}>
	   {component ClsItemBid entity_id=$item_info.item_id entity=$item_info.entity currency=$item_info.currency}
    </div>
</div>


{*View fullsize Thickbox*}
<div style="display: none;">
	<div class="item_file_title"><h2>{text %item_file}</h2></div>
	<div class="item_file_content"></div>
</div>

{*Delete Item Confirm*}	 
<div style="display: none">
	<div {id="confirm_title"}><h2>{text %delete_item_title}</h2></div>
	<div {id="confirm_content"}>{text %messages.delete_item_confirm}</div>
</div>

{/container}
{/canvas}