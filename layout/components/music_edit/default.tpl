{canvas}
{container}
	<div class="float_right" style="padding-bottom: 3px">
		<input type="button" value="{text %.forms.music_edit.view_label}" onclick="window.location='{document_url doc_key=music_view musickey=$hash}'">
	</div>
	<br clear="all" />
	<div class="float_half_left wider">
	{block title=%.forms.music_edit.label}
	{form MusicEdit}
		<table class="form">
			<tr>
				<td class="label">{label for='title'}</td>
				<td class="value all_row_width">{input name='title'}</td>
			</tr>
			<tr>
				<td class="label">{label for='description'}</td>
				<td class="value">{input name='description'}</td>
			</tr>
			<tr>
				<td class="label">{label for='privacy_status'}</td>
				<td class="value">{input name='privacy_status' labelsection='forms.music_edit.fields.privacy_status'}</td>
			</tr>
		</table>
		<div class="block_submit center">{button action='save'}</div><br />
	{/form}
	{/block}
	</div>
	
	{/container}
{/canvas}