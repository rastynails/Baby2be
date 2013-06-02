{* component Sign In Form *}

{container stylesheet="sign_in.style"}
	
	<div class="sign_in_cont-line">
		{form SignIn}
		<div class="clearfix">
			<div class="field">
				<div>{label for='login'}</div>
				<div>{input name='login' tabindex=1}</div>
				<div>{input name='remember_me'  tabindex=3 label=%.forms.sign_in.fields.remember_me.label}</div>
			</div>
			<div class="field">
				<div>{label for='password'}</div>
				<div>{input name='password'  tabindex=2}</div>
			</div>
			<div class="field button">
				{button action='process' tabindex=4}
			</div>
		</div>
		{/form}
		<div class="center forgot_block">{component ForgotPassword}</div>
		
	</div>
{/container}
