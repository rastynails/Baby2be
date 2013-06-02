
{canvas}

	{container}
	<br />
	<div class="float_half_left wide">
		{form AffiliateSignUp}
		{block title=%form_title}
		<table class="form all_row_width">
			<tr>
				<td class="label">{label for='full_name'}</td>
				<td class="value">{input name='full_name'}</td>
			</tr>
			<tr>
				<td class="label">{label for='email'}</td>
				<td class="value">{input name='email'}</td>
			</tr>
			<tr>
				<td class="label">{label for='password'}</td>
				<td class="value">{input name='password'}</td>
			</tr>
			<tr>
				<td class="label">{label for='password_conf'}</td>
				<td class="value">{input name='password_conf'}</td>
			</tr>
		</table><br />
		{input name='aff_captcha'}
		<div class="block_submit">{button action='signup'}</div>
		{/block}	
		{/form}
	</div>
	<div class="float_half_right narrow">
		{block title=%affiliate_tip_title class="tip"}{text %affiliate_tip}{/block}
		<a href="{document_url doc_key='affiliate_home'}">{text %affiliate_link}</a>
	</div>
	{/container}
	
{/canvas}