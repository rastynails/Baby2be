{* Httpdoc Sign In component *}

{canvas centered}
	{include_style file="sign_in.style"}
<div class="block_cap sign_in_cap"><div class="block_cap_r"><div class="block_cap_c"></div></div></div>
<div class="sign_in_bg">
	<div class="sign_in_document">
		<div class="bg_stretch clearfix">
		{if $sign_up}
			<div class="float_half_left">
				{component SignIn}
				<div class="img_angel"></div>
			</div>
			<div class="signin_signup_delimitr">{*text %or*}</div>
			<div class="float_half_right">
				{component SignUp}
				<div class="img_plane"></div>
			</div>
		{else}
			<div class="center_block">
				{component SignIn}
			</div>
		{/if}
	        </div>
         </div>
</div>
<div class="block_bottom"><div class="block_bottom_r"><div class="block_bottom_c"></div></div></div>
{/canvas}
