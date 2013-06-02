{* component Sign In Form *}

{container stylesheet="sign_in.style"}

<div class="sign_in_cont" {if $hidden}style="display: none"{/if}>
	
		{if $hidden}
			<div class="fb_title">
				{text %label}
			</div>
		{else}
			{block_cap title=%label}{/block_cap}
		{/if}
		
		<div class="fb_content">
		<div class="block_toolbar">{component ForgotPassword}</div>
		{block}
		
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
			
		{/block}	
		</div>
	
</div>
{/container}
