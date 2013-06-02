{* component Comment Add Form *}

{container}
<a {id="send_profile"} href="javascript://">{text %.components.send_profile.button_label}</a>
<div style="display:none;">
<div {id="send_profile_title"}>{text %.components.send_profile.button_label}</div>
<div {id="send_profile_cont"}>    
    {form SendProfile}
    	<div class="center">
        	<span>{text %.components.send_profile.info_text}</span><br /><br />
        	{input name='email'}<br />
        <div class="block_submit">{button action='send_profile}</div>
        </div>
    {/form}
</div>
</div>
{/container}