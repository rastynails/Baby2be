
{if !empty($service_msg)}
    <div class="not_found">{$service_msg}</div>
{else}
<div class="form">
<form method="post">
<div class="field">
	<label for="profile">{text section='mobile' key='send_message_to'}</label>
	<input type="text" name="profile" id="profile" value="{if isset($send_to)}{$send_to}{/if}" />
</div>
<div class="field">
	<label for="subject">{text section='forms.send_message.fields' key='subject'}:</label>
	<input type="text" name="subject" id="subject" />
</div>
<div class="field">
	<label for="message">{text section='mobile' key='message'}</label>
	<textarea name="message" id="message"></textarea>
</div>
<div class="submit">
	<input type="submit" value="{text section='forms.send_message.actions' key='send'}" />
</div>
</form>
<div class="clr"></div>
</div>
<div class="clr"></div>
{/if}