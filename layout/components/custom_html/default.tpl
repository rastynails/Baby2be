{container stylesheet="style.style"}
{if $owner && !$permission}
<div style="display:none;">

<div {id="edit_form_label"}>{text %.components.custom_html.html_edit_cap_label}</div>

<div {id="edit_form"}>

{form CustomHtmlEdit}

{input name='html_cap'}
<br />
{input name='custom_html'}
{input name='cmp_id'}
<div style="padding:5px;text-align:right;">{button action='custom_html_edit'}</div>
{/form}

</div>
</div>
{/if}
{block}
	{block_cap title=$cap_label}
		{if $owner}
			<a class="delete_cmp" href="javascript://"></a>
			{if !$permission}<a {id="edit_cmp"} class="edit_cmp" href="javascript://"></a>{/if}
		{/if}
	{/block_cap}
	{if !$permission}
		{$html}
	{else}
		{$permission}
	{/if}
{/block}
{/container}