{* Httpdoc Sign In component *}

{canvas centered}
	{include_style file="sign_in.style"}
<div class="sign_in_bg">	
	<div class="sign_in_document">
		{if $sign_up}
			<div class="float_half_left">
				<div class="sign_in_sign_in">
						{component SignIn}
				</div>
				</div>
				<div class="signin_signup_delimitr">OR</div>
				<div class="float_half_right">
				<div class="sign_up_on_sign">
						{component SignUp}
				</div>
			</div>
		{else}
			<div class="center_block">
				<div class="sign_in_sign_in">
						{component SignIn}
				</div>
			</div>
		{/if}
	</div>
    </div>
{/canvas}
