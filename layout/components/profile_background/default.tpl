{container stylesheet="style.style" class="prof_bg_cont"}

{if $not_owner}
{else}
	{if $permission.is_available}
	<div style="display:none;">
	
	<div {id="cp_cap_label"}>{text %.components.profile_background.cap_label_set_bg_color}</div>
	
	<div {id="color_picker_cont"} class="color_picker_wrap">
	<div {id="color_picker"}>
	</div>
	<div style="text-align:right;padding:5px;">
	<input type="button" value="{text %.components.profile_background.btn_label_no_color}" {id="no_color"} />
	<input type="button" value="{text %.components.profile_background.btn_label_cancel}" {id="cancel"} />
	</div>
	</div>
	
	<div {id="image_cap_label"}>{text %.components.profile_background.cap_label_set_bg_image}</div>
	
	
	<div {id="bg_image_cont"}>
	
	{form BgImageUpload}
	
	<table>
	<tr>
		<td><label><input type="radio" {id="upload_radio"} name="image_select" /> {text %.components.profile_background.field_label_upload_image}</label></td>
	    <td style="padding:4px;">
	    	{if $img_url}<div {id="img_file_cont"}><img src="{$img_url}" width="100" /> <a href="javascript://">Delete</a></div>{/if}
	         <div {id="input_file_cont"}{if $img_url} style="display:none;"{/if}>{input name='file'}</div>
	    </td>
	</tr>
	<tr>
		<td><label><input type="radio" {id="url_radio"} name="image_select" /> {text %.components.profile_background.field_label_image_url}</label></td>
	    <td style="padding:4px;">{input name='image_url'}</td>
	</tr>
	</table>
	<div style="text-align:right;padding:5px;"><input type="button" value="{text %.components.profile_background.btn_label_no_image}" {id="no_image"} /> {button action='bg_image_upload'}</div>
	{/form}
	
	</div>
	
	</div>
	
	<input type="button" value="{text %.components.profile_background.btn_label_bg_color}" {id="bg_color"} />
	<input type="button" value="{text %.components.profile_background.btn_label_bg_image}" {id="bg_image"} />
	{else}
		{$permission.message}
	{/if}
{/if}

{/container}