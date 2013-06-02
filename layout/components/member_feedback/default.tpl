{* Component Member Feedback *}

{canvas}
{container}

{if !$message}

{block title=%title}
	{form MemberFeedback}
	<table class="form">
		<tr>
			<td class="label">{label for="feedback_to"}</td>
			<td class="value">{input name="feedback_to"}</td>
		</tr>
		{if !$profile_id}
		<tr>
			<td class="label">{label for="member_email"}</td>
			<td class="value">{input name="member_email"}</td>
		</tr>
		{/if}
        <tr>
		<tr>
			<td class="label">{label for="subject"}</td>
			<td class="value">{input name="subject"}</td>
		</tr>		
		<tr>
			<td class="label">{label for="message"}</td>
			<td class="value">{input name="message"}</td>
		</tr>	
		<tr>
			<td class="label">{label for="captcha_label"}</td>
			<td class="value">{input name="captcha"}</td>
		</tr>								
	    <tr><td colspan="2" class="submit">{button action="send"}</td></tr>
	 </table>
	 {/form}
{/block}

{else}
	<div class="no_content">
		{$message}
	</div>
{/if}

{/container}
{/canvas}