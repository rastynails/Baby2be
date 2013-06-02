{* component Invite Friends *}


 {container stylesheet="invite_friends.style" class="invite_friends_cont"}
        {if $contactGrabber}
 	<div {id="box_title"} style="display:none"><b>{text %import.box_label}</b></div>
	<div {id="box_content"} style="display:none">
	<div class="import_container">

			<div class="auth_cont">
					<div class="mobile_cont">
						<form>
							<table class="form">
								<tbody>
									<tr>
										<td class="label">{text %import.email}</td><td class="value"><input type="text" name="email"></td>
									</tr>
									<tr>
										<td class="label">{text %import.password}</td><td class="value"><input type="password" name="password"></td>
									</tr>
									<tr>
										<td class="label">{text %import.provider}</td>
										<td class="value">
											<select name="provider" class="provider">
												{foreach from=$providers item='provider' key="key"}
													<option value="{$key}">{$provider.name}</option>
												{/foreach}
											</select>
										</td>
									</tr>
								</tbody>
							</table>
							<p align="center"><input type="submit" value="{text %import.get_contacts_btn}"></p>
						</form>
					</div>
			</div>
			<div class="contact_list" style="display:none">
			<!--<div class="open_list pointer">{block_cap title=%import.contact_list}<div class="drop_list_icon"></div>{/block_cap}</div>-->
				{block class="nomargin" title=%import.contact_list expandable=yes id="contact_list"}
					<div  class="list_items_cont">
						<table width="98%" class="form">
							<tbody>
								<tr>
									<td class="check"></td>
									<td class="name label"><b>{text %import.name_label}</b></td>
									<td class="address value"><b>{text %import.address_label}</b></td>
								</tr>
								<tr class="empty_list" style="display: none">
									<td colspan="3" class="center">{text %import.empty_list_text}</td>
								</tr>
								<tr class="contact_item list_item prototype_node">
									<td class="check"><input type="checkbox"></td>
									<td class="name label info"></td>
									<td class="address value info"></td>
								</tr>
							</tbody>
						</table>
					</div>
					<!--<div class="mobile_cont" style="display:none">
						<div class="list_items_cont">
							<div class="contact_item list_item prototype_node">
								<div class="check"><input type="checkbox"></div>
								<div class="info">
									<div class="name"></div>
									<div class="address"></div>
									<br clear="all" />
								</div>
								<br clear="all"	/>
							</div>
						</div>-->
						<p class="check_all"><input type="checkbox"}>&nbsp;{text %import.check_all}</p>

						<p align="center"><input type="button" name="add_contact" value="{text %import.add_contact_btn}"></p>
				{/block}
			</div>
		</div>
	</div>
	{/if}
	<div class="invite_form_cont">
	 	{block}
		{block_cap title=%block_label}
	 		{component ContactImporterButtons}
 		{/block_cap}
		 	{form InviteFriends}
			        <div class="clearfix">
				<p class="invite_col1">
					{label for='email_addr'}
					{input name='email_addr'}
			 	</p>
			 	<p class="invite_col2">
					{label for='message'}
					{input name='message'}
			 	</p>
				<br clear="all" />
				{if $contactGrabber}<p class="invite_import"><a href="javascript://" {id="open_box_btn"}>{text %import.open_btn}</a></p>{/if}
			 	<p align="right" class="invite_btn">
					{button action='process'}
				</p>
				</div>
			 {/form}
		{/block}
 	</div>
 {/container}
