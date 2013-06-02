
{canvas}
	{container}
		<div class="center_block">
		{block title=%.forms.group_send_message.title}
			{if isset($error)}
				<div class="no_content">{$error}</div>
			{else}
			{form GroupSendMessage}
				<table class="form">
                <tbody>
                	<tr>
                        <td class="label">{label for='subject'}</td>
                        <td class="value">{input name='subject'}</td>
                    </tr>
                    <tr>
                        <td class="label">{label for='message'}</td>
                        <td class="value">{text_formatter for='message' entity="group"}{input name='message'}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="block_submit right">{button action='send'}</td>
                    </tr>
                </tbody>
            </table>
			{/form}
			{/if}
		{/block}		
		</div>
	{/container}
{/canvas}