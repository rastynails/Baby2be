{* Profile Views List Http Document*}

{canvas}
	{include_style file="profile_views_list.style"}
	{container}
		<div class="controls">
			<div class="period_container">
				<form action="" method="GET">
					<div>{text %history_for} :
						<select name="period" onchange="this.form.submit();">
							<option {if $period eq 'month'}selected="selected"{/if} value="month">{text %month_label}</option>
							<option {if $period eq 'week'}selected="selected"{/if} value="week">{text %week_label}</option>			
							<option {if $period eq 'day'}selected="selected"{/if} value="day">{text %day_label}</option>			
						</select>		
					</div>
				</form>
			</div>
		</div>
		<div class="list_container">
			{component ProfileViewsList}
		</div>
	{/container}
{/canvas}