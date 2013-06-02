{* component Unregister Profile *}
{canvas}

	{container class="unregister_profile" stylesheet="unregister_profile.style"}
		<div class="center_block">
			{form UnregisterProfile}
				<div class="text">
					<div class="warning">{text %labels.warning}</div>
					<p>{text %labels.leave_comment}</p>
				</div>
                <div style="display: none;">{label %.forms.report.fields.reason for="reason"}</div>
				{input name="reason" class="area_small"}
				<div class="center">{button action=unregister}</div>
			{/form}
		</div>	
	{/container}

{/canvas}
