
{block}
	<div class="brief_info_thumb">{profile_thumb profile_id=$opponent_id size=70}</div>
	<div class="brief_info_cont">{text section='mobile' key='chat_with'} <a href="{$profile_url}{$opponent}">{$opponent}</a></div>
	{if !empty($service_msg)}
	   <div class="not_found">{$service_msg}</div>
	{else}
	<iframe src="{$iframe_src}#type" style="width: 99%" class="imiframe"></iframe>
	
	<form method="post">
		<input type="hidden" name="" />
		<input type="text" name="msg" tabindex="0" />
		<input type="submit" value="Send" />
	</form>
	{/if}
{/block}