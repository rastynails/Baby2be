{container stylesheet="style.style"}
{if $owner && !$permission}
<div style="display:none;">

<div {id="edit_form_label"}>{text %.components.m_player.edit_cap_label}</div>

<div {id="edit_form"}>

{form MPlayerEdit}

{input name='custom_html'}
{input name='cmp_id'}
<div style="padding:5px;text-align:right;">{button action='m_player_edit'}</div>
{/form}

</div>
</div>
{/if}
{literal}
<style>
body{
	/*background-color:red !important;*/
}
</style>
{/literal}
{block}
	{block_cap title=%.components.m_player.cap_label}
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