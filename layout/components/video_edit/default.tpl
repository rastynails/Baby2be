
{canvas}
{container}
	<div class="float_right" style="padding-bottom: 3px">
		<input type="button" value="{text %.forms.video_edit.view_label}" onclick="window.location='{document_url doc_key=profile_video_view videokey=$hash}'">
	</div>
	<br clear="all" />
	<div {id="video_edit"} class="float_half_left wider">
	{block title=%.forms.video_edit.label}
	{form VideoEdit}
		<table class="form">
			<tr>
				<td class="label">{label for='title'}</td>
				<td class="value all_row_width">{input name='title'}</td>
			</tr>
			<tr>
				<td class="label">{label for='description'}</td>
				<td class="value">{input name='description'}</td>
			</tr>
			{if $enable_categories}
			<tr>
                <td class="label">{label for='category'}</td>
                <td class="value">{input name='category' labelsection='video_categories'}</td>
            </tr>
			{/if}
			<tr>
				<td class="label">{label for='privacy_status'}</td>
				<td class="value">{input name='privacy_status' labelsection='forms.video_edit.fields.privacy_status'}</td>
			</tr>
			<tr class="password_tr" {if $video.password == ''}style="display: none;"{/if}><td class="label">{label for='password'}</td><td class="value all_row_width">{input name='password'}</td></tr>
		</table>
		<div class="block_submit center">{button action='save'}</div><br />
	{/form}
	{/block}
	</div>
	
	{if $tags_cmp}<div class="float_half_right narrower">{component $tags_cmp}</div>{/if}
{/container}
{/canvas}