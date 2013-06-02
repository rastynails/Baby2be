{* component Report Form *}

{if $reporter_id}
{container class="report"}
	{if $show_link}
	 <a {id="button_show"} href="javascript://">{text %link_report}</a> 
	{else}
	<input {id="button_show"} type="button" value="{text %btn_report}" /> 
	{/if}
	<div {id="report_cont"} style="display: none">
		{form Report reporter_id=$reporter_id entity_id=$entity_id type=$type}
			{label %.forms.report.fields.reason for='reason' }: {input name='reason' class='area_small'}
			<p class="center">{button action='add'}</p>
		{/form}
	</div>
{/container}
{/if}
