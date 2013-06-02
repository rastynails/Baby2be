
{container stylesheet="video_upload.style" class="video_upload"}
	{block title=%tips class="tip"}
		{text %tips_content}
	{/block}

{if $permission_message}
	<div class="block_info">{$permission_message}</div>
{else}
	{block_cap title=%title}
		{menu type="ajax-block" items=$video_upload_menu_items}
	{/block_cap}
	{block}
	<div class="frm_wrapper">
		<div {id="upload_file_form"} {if $vs == 'embed_code'}style="display: none;"{/if}>
		{form VideoUpload}
			<table class="form">
				<tr><td class="label">{label for='title'}</td><td class="value all_row_width">{input name='title'}</td></tr>
				<tr><td class="label">{label for='description'}</td><td class="value">{input name='description'}</td></tr>
				<tr><td class="label">{label for='tag'}</td><td class="value all_row_width">{input name='tag'}</td></tr>
				{if $enable_categories}
				    <tr><td class="label">{label for='category'}</td><td class="value">{input name='category' labelsection='video_categories'}</td></tr>
				{/if}
				<tr><td class="label">{label for='privacy_status'}</td><td class="value">{input name='privacy_status' labelsection='forms.video_upload.fields.privacy_status'}</td></tr>
				<tr class="password_tr" style="display: none;"><td class="label">{label for='password'}</td><td class="value all_row_width">{input name='password'}</td></tr>
				<tr><td class="label">{label for='profile_video'}</td><td class="value">{input name="profile_video"}</td></tr>
				<tr><td colspan="2" class="center td_input">{button action='upload'}</td></tr>
			</table>			
		{/form}
		</div>

		<div {id="embed_code_form"}" {if $vs == 'file'}style="display: none;"{/if}>
		{form VideoUpload}
			<table class="form">
				<tr><td class="label">{label for='title'}</td><td class="value all_row_width">{input name='title'}</td></tr>
				<tr><td class="label">{label for='description'}</td><td class="value">{input name='description'}</td></tr>
				<tr><td class="label">{label for='tag'}</td><td class="value all_row_width">{input name='tag'}</td></tr>
				{if $enable_categories}
				    <tr><td class="label">{label for='category'}</td><td class="value">{input name='category' labelsection='video_categories'}</td></tr>
				{/if}
				<tr><td class="label">{label for='privacy_status'}</td><td class="value">{input name='privacy_status' labelsection='forms.video_upload.fields.privacy_status'}</td></tr>
				<tr class="password_tr" style="display: none;"><td class="label">{label for='password'}</td><td class="value all_row_width">{input name='password'}</td></tr>
				<tr><td class="label">{label for='code'}</td><td class="value"><span class="small">{text %embed_text}</span>{input name='code'}</td></tr>
				<tr><td colspan="2" class="center td_input">{button action='embed'}</td></tr>
			</table>			
		{/form}
		</div>
	</div>	
	{/block}
{/if}	
{/container}