{* Httpdoc Sign In component *}

{canvas centered}
	{include_style file="sign_in.style"}
<div class="sign_in_bg">
	<div class="sign_in_document">
		<div class="bg_stretch">
		{if $sign_up}
			<div class="float_half_left">
				{component SignIn}
			</div>
			<div class="signin_signup_delimitr">{*text %or*}</div>
			<div class="float_half_right">
				{component SignUp}
			</div>
		{else}
			<div class="center_block">
				{component SignIn}
			</div>
		{/if}
		<div class="clr_div"></div>
	</div>
</div>	
{/canvas}
