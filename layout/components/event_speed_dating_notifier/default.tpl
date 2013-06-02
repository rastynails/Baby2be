<div class="event_speed_dating_container" {if $display} style='display: block;' {else} style='display: none;' {/if}>
	{block title=%.event.speed_dating_events }
		<div id="event_speed_dating_status">
			<label id='esd_profile_in_session'></label>
			<br />			
			<label id='esd_elapsed_time'></label>
            <div style="text-align:left; margin-top:5px;"><input class="pcv_add_cmps" type="button" id="esd_notifier_next_member" value="{text %.components.event.speed_dating.esd_notifier_next_member_label}" disabled="disabled" /></div>
            <div style="text-align:left; margin-top:15px; display: none;" id='esd_notifier_reopen_container'><input class="pcv_add_cmps" type="button" id="esd_notifier_reopen" value="{text %.components.event.speed_dating.esd_notifier_reopen_label}"  /></div>
		</div>
		{container stylesheet="style.style"}
		
		<div {id="event_speed_dating_notifier_content"} style="display:none;">
		<input type='hidden' id="esd_notifier_countdown" />
		<span class="title">{text %.components.event.speed_dating.notifier_cap_title}</span>
		    <span class="content">
		    	<span class="block_body"></span>
				<br clear="all" />
				<div style="text-align:center"><input class="pcv_add_cmps" type="button" {id="esd_notifier_submit"} value="{text %.components.event.speed_dating.esd_notifier_submit_label}" /></div>
			</span>
		</div>
		{/container}

		<div class="clr"></div>
	{/block}
</div>