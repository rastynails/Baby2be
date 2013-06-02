{* Component Member Console *}

{container class="member_console" stylesheet="member_console.style"}
	
	{capture name=toolbar}
		<a href="{document_url doc_key=sign_out}">{text %sign_out}</a>
	{/capture}
	
	{block class=thumb_box title=$profile.username|truncate:18:"...":true toolbar=$smarty.capture.toolbar}
		
			<div class="thumb_container">
				{profile_thumb profile_id=$profile.id href=$profile.photo_upload_url size=100 sexLine=false}
			</div>

			<div class="center">
			{if $membership}
				<div class="membership_container">{membership_icon profile_id=$profile.id} {$membership}</div>
			{/if}
			<div class="clr"></div>
			  <div style="display:none"><input id="delete_t_text" value="{text %delete_thumb_msg}" /></div>
			  {if $profile.hasThumb}
                            <a href="javascript://" class="delete_thumb">{text %delete_thumb}</a>
                          {/if}
		        </div>
	{/block}
	
	{component IMListener}
	
	{component ProfileNotices}
	
	{block title=%my_profile expandable=yes expanded=$console_menu_opened id="console_menu"}
		<div class="my_profile_menu_cont">
			{menu type=vertical items=$console_menu}
		</div>
	{/block}
	
	{block title=%add_new class="block_null"}
		<div class="new_items super_small clearfix">
		{foreach from=$new_items_menu item="item"}
			<a href="{$item.url}">
				<div class="{$item.class}"></div>
				{$item.label}
			</a>
		{/foreach}
			<div class="clr"></div>
		</div>
	{/block}
	
	{component EventSpeedDatingNotifier} 

	
{/container}