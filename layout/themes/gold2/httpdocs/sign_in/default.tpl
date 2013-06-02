{* Httpdoc Sign In component *}

{canvas centered}
	{include_style file="sign_in.style"}
    	{* <div class="sign_in_open"></div> *}

	<div class="sign_in_document clearfix">
		{if $sign_up}
			<div class="float_half_left">
				{component SignIn}
			</div>
			<div class="signin_signup_delimitr">{text %or}</div>
			<div class="float_half_right">
				{component SignUp}
			</div>
		{else}
			<div class="center_block">
				{component SignIn}
			</div>
		{/if}	
	</div>
{/canvas}
