{* component Ajax Chat *}

{canvas}

{container stylesheet="chat.style"}

{if $no_permission}
	<div class="no_content">{$no_permission}</div>
{else}
	<div style="display: none">
		{* chat room template node *}
		{block title="" id="chat_room_prototype" class="chat_room_block" expandable=yes}
			{block_cap}
				<sup class="users_count"></sup>
			{/block_cap}
			<ul class="chat_room_users">
				<li class="loading">{text %loading}</li>
			</ul>
		{/block}
		
		{* room window template node *}
		<ul {id="chat_room_window_prototype"} class="chat_room_window" style="display: none"></ul>
		
		<ul style="display: none">
			{* room user template node *}
			<li {id="chat_room_user_prototype"} class="chat_room_user">
				<a class="chat_room_user" href="#" {if isset($is_123wm)}onclick="window.initiate123wm($(this).html());"{/if}></a>
			</li>
			{* message template node *}
			<li {id="chat_win_msg_prototype"} class="chat_win_msg">
				<span class="msg_time small"></span><a href="#" class="msg_username"></a><span class="msg_text"></span>
			</li>
		</ul>
	</div>
	
	{* user context menu *}
	<ul {id="chat_user_context_menu"} class="context_menu" style="display: none">
		<li><a {id="chat_user_context_menu_im_btn"} href="#">{text %send_private_msg}</a></li>
		<li class="context_menu_separator"></li>
		<li><a {id="chat_user_context_menu_profile_link"} href="#" target="_blank">{text %view_profile}</a></li>
	</ul>
	
	{* chat carcass table *}
	<table class="chat_carcass_tbl" cellspacing="0">
		<tbody>
			<tr>
				<td>
					<div class="chat_bottom_container">
					<form {id="chat_input_form"} onsubmit="return false">
						<div class="chat_bottom_inventory">
							{palette_picker id="msg_color"}
							{smileset for="chat_input"}
						</div>
						<div style="clear: both;"></div>
						<div class="chat_input_container">
							<input type="text" {id="chat_input"} name="text_entry" class="chat_input" />
							<input type="submit" {id="send_btn"} class="chat_send_btn" value="{text %send_button}" />
						</div>
					</form>
					</div>
				</td>
			</tr>
			<tr>
				<td class="chat_windows_cell">
					{block_cap id="active_window_title" title=%loading}{/block_cap}
					<div {id="chat_windows_container"} class="chat_windows_container">
						<div style="height: 1px"></div>
					</div>
				</td>
				<td class="chat_rooms_cell">
					{block_cap title=%rooms}{/block_cap}
					<div {id="chat_rooms_container"} class="chat_rooms_container"></div>
				</td>
			</tr>
		</tbody>
	</table>
{/if}
	
{/container}

{/canvas}
