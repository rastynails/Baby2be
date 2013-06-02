{* Httpdoc Sign In component *}

{canvas centered}
	{include_style file="sign_in.style"}
<div>
	<div class="sign_in_open"></div>
	<div class="sign_in_document">
		{if $sign_up}
			<div class="float_half_left">
				{component SignIn}
			</div>
			<div class="signin_signup_delimitr">OR</div>
			<div class="float_half_right">
				{component SignUp}
			</div>
		{else}
			<div class="center_block">
				{component SignIn}
			</div>
		{/if}	
	</div>
</div>	
{/canvas}
