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
		{block}
		
				{form SignIn}
					<div class="field">
						<div style="display:none">{label for='login'}</div>
						<div class="input_name">{input name='login' hasInvitation=yes}</div>
					</div>
					<div class="field">
						<div style="display:none">{label for='password'}</div>
						<div class="input_pass">{input name='password' hasInvitation=yes}</div>
					</div>
					<div class="clearfix">
						<p class="sign_in_btn">
							{button action='process'}
						</p>
						<div class="remember">
							{input name='remember_me' label=%.forms.sign_in.fields.remember_me.label}
						</div>
					</div>
				{/form}	
		             <div class="block_toolbar">{component ForgotPassword}</div>
			
		{/block}
		
		</div>
	
</div>
{/container}
