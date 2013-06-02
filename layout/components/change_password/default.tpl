{* component Change Password *}

{container class="change_password" stylesheet="change_password.style"}

	<a href="javascript://" {id="btn"}>{text %label}</a>
	<div {id="thick_title"} style="display:none"><b>{text %label}</b></div>
	<div {id="thick_content"} style="display:none">
		{form ChangePassword}
		<table class="form change_pass">
			<tr>
				<td class="label">{label for="old_password"}</td><td class="value">{input name="old_password"}</td>
			</tr>
			<tr>
				<td class="label">{label for="new_password"}</td><td class="value">{input name="new_password"}</td>
			</tr>
			<tr>
				<td class="label">{label for="new_password_confirm"}</td><td class="value">{input name="new_password_confirm"}</td>
			</tr>
			<tr>
				<td align="right" colspan="2">{button action="change"}<input type="button" {id="cancel"} value="{text %.forms.change_password.actions.cancel}"></td>
			</tr>
		</table>
		{/form}
	</div>
	
{/container}

