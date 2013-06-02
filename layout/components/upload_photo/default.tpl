{* Component Upload Photo *}

{canvas}
	{container stylesheet="upload_photo.style" class="upload_photo clearfix"}
		<div style="display: none">
			<div {id="confirm_title"}>{text %upload.delet_confirm_title}</div>
			<div {id="confirm_content"}>{text %upload.delet_confirm_content}</div>
		</div>
		{if $is_general_album}
			{component PhotoTips}
		{/if}
		
		<div class="details_cont narrow">
			<div {id="photo_details"}>		
				{capture name=toolbar}
					<div class="photo_toolbar" style="display:none">
						<a href="javascript://" class="permalink">{text %upload.permalink}</a>
					
						<a href="javascript://" class="make_thumb">{text %upload.make_thumb}</a> |
						<a href="javascript://" class="delete">{text %upload.delete}</a>
						
					</div>
				{/capture}
					{block title=%upload.title toolbar=$smarty.capture.toolbar}
					
						<div class="photo_details_container">
						
							<div class="upload_form empty" style="display: none">
								<div class="permission_msg" style="display: none">
									<div class="block_info message"></div>
								</div>
								<div class="form">
									{form UploadPhoto}
										{input name="upload_photo" height="250px"}
									{/form}
								</div>	
							</div>
							
							<div class="photo_details">
							    <div class="image_in_progress" style="display: none">
                                </div>
								<div class="preview large_image" style="display: none">
								    {if $allow_rotate}
									    <div class="image_controls">
									        <div class="rotate_ccw"></div>
									        <div class="rotate_cw"></div>
									    </div>
									{/if}
								</div>
								<div class="preview_empty empty">
									<p class="block_info">{text %upload.preview_empty}</p>
								</div>
								<div class="photo_info">
									<div class="status_cont" style="display: none">
										{text %upload.status} :&nbsp;
										<span class="approval" style="display: none">{text %upload.admin_status.approval}</span>
										<span class="active" style="display: none">{text %upload.admin_status.active}</span>
										<span class="suspended" style="display: none">{text %upload.admin_status.suspended}</span>
									</div>
									{if $photo_ver_avaliable}
										<div class="photo_auth">
										    <span class="authed" style="display: none">{text %upload.authed}</span>
	                                        <span class="unauthed" style="display: none">
	                                            <a href="{document_url doc_key=photo_auth}">{text %upload.unauthed}</a>
	                                        </span>
										</div>
									{/if}
									<div style="clear: both;"></div>
								</div>
							</div>
							
							<div class="photo_info" style="display: none">
								{capture name="info_toolbar"}
									<a class="add" style="display: none" href="javascript://">{text %upload.add_info}</a>
									<a class="edit" style="display: none" href="javascript://">{text %upload.edit_info}</a>
								{/capture}
								{block title=%upload.description toolbar=$smarty.capture.info_toolbar}
																
									<div class="info_cont" style="display: none">
										<table class="form">
												<tbody>
													<tr>
														<td class="label">{text %upload.title_label}:</td>
														<td class="title value"></td>
													<tr>
													</tr>
														<td class="label">{text %upload.description_label}:</td>
														<td class="description value"></td>
													</tr>
												</tbody>
										</table>
									</div>
									<div class="info_form" style="display: none">
										<form>
											<table class="form">
												<tbody>
													<tr>
														<td class="label">{text %upload.title_label}:</td>
														<td class="value"><input type="text" name="title"></td>
													<tr>
													</tr>
														<td class="label">{text %upload.description_label}:</td>
														<td class="value"><textarea name="description" class="area_small"></textarea></td>
													</tr>
												</tbody>
											</table>
											
											<p align="right"><input type="submit" value="{text %upload.save_info}"> <input type="button" name="cancel" value="{text %upload.cancel_info}"></p>
										</form>
									</div>
									
								{/block}	
							</div>
						
							
							<div class="status_cont" style="display: none">
								{if count($publishing_statuses) > 1}
									{block title=%upload.additional_info}
									<table class="add_info_table">
										{if $move_to}
											<tr>
												<td>
													{text %upload.move_to}
												</td>
												<td class="album_select_cont">
													<select class="album_select">
														{if $display_general}
															<option value="" class="general_label">
																{text %albums.general_label}
															</option>
														{/if}
														{foreach from=$albums item=item}
															<option value="{$item->getId()}">
																{$item->getView_label()}
															</option>
														{/foreach}
													</select>
													<input type="button" class="move" value="{text %albums.move_action}">
												</td>
											</tr>
										{/if}
											<tr>
												<td>
													{text %upload.publishing_status}
												</td>
												<td class="album_select_cont">
													<select name="status">
														{foreach from=$publishing_statuses item="item"}
															<option value="{$item}">{text %upload.status.`$item`}</option>
														{/foreach}
													</select>
													
												</td>
											</tr>
											<tr class="password" style="display: none">
												<td>
													{text %upload.password_label}
												</td>
												<td>
														<form>
															<input type="text" name="password">
															<input type="submit" value="{text %upload.save_password}">
														</form>
												</td>
											</tr>
									</table>
										
									{/block}
								{/if}
							</div>
							
						</div>
						
					{/block}
			</div>	
		</div>
		<div class="photo_list_container wide">
		
			{block title=%list.title}
				<div style="position:relative">
					<div class="preloader list_preloader" style="display:none"></div>
					<div>
						<div {id="photo_list"} class="photo_list">
							<div class="prototype_node slot" style="display:none">
							</div>
							
						</div>
					</div>
				</div>
			
			{/block}
			
		</div>
	
	{/container}
{/canvas}