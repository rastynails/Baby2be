
{canvas}
	{container}
		<div class="center_block wide">
			{$message}
			{if $show_form}	
			{block title=%unsubscribe}
				{form Unsubscribe}
					{if $pwd_field}
					<table class="form">
						<tr>
							<td class="label">{label %.forms.unsubscribe.fields.pass for='pass'}:</td>
							<td class="value">{input name='pass'}</td>
						</tr>
					</table><br />
					<div class="right">{button action='process'}</div>
					{else}
						<br /><div class="center">{* text %instruction *} {button action='process'}</div>
					{/if}
				{/form}
			{/block}
			{/if}
		</div>
	{/container}
{/canvas}