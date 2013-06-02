{* component Sign In Form *}

{container stylesheet="sign_in.style"}

<div class="sign_in_cont{if $type} sign_in_cont-{$type}{/if}">
	<div class="forgot_password">{component ForgotPassword}</div>
	{form SignIn}
		<div class="field">
			<div>{label for='login'}</div>
			<div>{input name='login'}</div>
		</div>
		<div class="field">
			<div>{label for='password'}</div>
			<div>{input name='password'}</div>
		</div>
		<div>
			{input name='remember_me' label=%.forms.sign_in.fields.remember_me.label}
		</div>
		<p align="right">
			{button action='process'}
		</p>
	{/form}	
</div>
{/container}
