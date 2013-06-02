{* component Forgot Password *}

{container class="forgot_password" stylesheet="forgot_password.style"}

	<a href="javascript://" {id="btn"} class="forgot_pass_btn">{text %label}</a>
	<div {id="thick_title"} style="display:none"><b>{text %label}</b></div>
	<div {id="thick_content"} style="display:none">
		<div class="block_info">{text %message}</div>
		{form ForgotPassword}
			<div class="float_left">
				{label for="email"} : {input name="email"}
			</div>
			<div class="controls center float_left">
				{button action="send"}
			</div>
			<div class="clr"></div>
		{/form}
	</div>
	
{/container}

