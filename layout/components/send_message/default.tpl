{container}

{if $type eq 'reply'}
	{block title=%reply_label}
		{form SendMessage conversation_id=$conversation_id sender_id=$sender_id recipient_id=$recipient_id type=$type}
			{text_formatter for='text' entity="mailbox" controls="bold,italic,underline,link,emoticon,image"}<br clear="all" />
			{label %.forms.send_message.fields.text for='text'}:
			{input name="text"}
			<p class="center">{button action='reply'}</p>
		{/form}
	{/block}
{else}
	<a href="javascript://" {id="send_message_link"}>{text %send_label}</a>
	<div style="display: none" {id="send_message_cont"}>
		{form SendMessage sender_id=$sender_id recipient_id=$recipient_id type=$type}
		<table class="form">
			<tr>
				<td class="label">{label %.forms.send_message.fields.subject for='subject'}:</td>
				<td class="value all_row_width">{input name="subject"}</td>
			</tr>
			<tr>
				<td class="label">{label %.forms.send_message.fields.text for='text'}:</td>
				<td class="value">{text_formatter for='text' entity="mailbox"}{input name="text"}</td>
			</tr>
		</table>
		<br />
		<p class="center">{button action='send'}</p>
		{/form}
	</div>
{/if}

{/container}