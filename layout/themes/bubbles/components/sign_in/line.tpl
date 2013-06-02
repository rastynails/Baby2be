{* component Sign In Form *}

{container stylesheet="sign_in.style"}
	
	<div class="sign_in_cont-line clearfix">
		{form SignIn}
			<div class="field" >
				<div class="sign_in_left">{label for='login'}</div>
				<div class="sign_in_left">{input name='login' tabindex=1}</div>
			</div>
			<div class="field" >
				<div class="sign_in_left">{label for='password'}</div>
				<div class="sign_in_left">{input name='password'  tabindex=2}</div>
			</div>
			<div class="field">
				<div>{input name='remember_me'  tabindex=3 label=%.forms.sign_in.fields.remember_me.label}</div>

			</div>
			<div class="field button">
				{button action='process' tabindex=4}
			</div>
		{/form}
		{component ForgotPassword}
		
	</div>
{/container}
