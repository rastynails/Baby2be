{* Httpdoc Sign In component *}

{canvas centered}
	{include_style file="sign_in.style"}
    <div style="background: url({$smarty.const.URL_LAYOUT}themes/rr/img/index_logo.jpg) no-repeat; width: 294px; height: 55px; margin-left: 150px;"></div>
<div>
	<div class="sign_in_document">
    	<div style="width: 540px; margin: auto;">
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
</div>	
{/canvas}
