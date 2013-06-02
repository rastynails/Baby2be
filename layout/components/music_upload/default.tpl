{container stylesheet="music_upload.style" class="video_upload"}
	{block title=%tips class="tip"}
		{text %tips_content}
	{/block}

{if $permission_message}
	<div class="block_info">{$permission_message}</div>
{else}
	{block_cap title=%title}
		{menu type="ajax-block" items=$music_upload_menu_items}
	{/block_cap}
	{block}
	<div class="frm_wrapper">
		<div {id="upload_file_form"} {if $ms == 'embed_code'}style="display: none;"{/if}>
		{form MusicUpload}
			<table class="form">
				<tr><td class="label">{label for='title'}</td><td class="value all_row_width">{input name='title'}</td></tr>
				<tr><td class="label">{label for='description'}</td><td class="value">{input name='description'}</td></tr>
				<tr><td class="label">{label for='privacy_status'}</td><td class="value">{input name='privacy_status' labelsection='forms.music_upload.fields.privacy_status'}</td></tr>
				<tr><td class="label">{label for='music_file'}</td><td class="value">{input name="music_file"}</td></tr>
				<tr><td colspan="2" class="center">{button action='upload'}</td></tr>
			</table>			
		{/form}
		</div>

		<div {id="embed_code_form"}" {if $ms == 'file'}style="display: none;"{/if}>
		{form MusicUpload}
			<table class="form">
				<tr><td class="label">{label for='title'}</td><td class="value all_row_width">{input name='title'}</td></tr>
				<tr><td class="label">{label for='description'}</td><td class="value">{input name='description'}</td></tr>
				<tr><td class="label">{label for='privacy_status'}</td><td class="value">{input name='privacy_status' labelsection='forms.music_upload.fields.privacy_status'}</td></tr>
				<tr><td class="label">{label for='code'}</td><td class="value"><span class="small">{text %.forms.music_upload.embed_text}</span>{input name='code'}</td></tr>
				<tr><td colspan="2" class="center">{button action='embed'}</td></tr>
			</table>			
		{/form}
		</div>
	</div>	
	{/block}
{/if}	
{/container}