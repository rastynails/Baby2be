{container}
<br />
<div class="center_block narrow">
{block_cap title=%label}{/block_cap}
{block}
	{form ProviderSecurepay sale_info_id=$sale_info_id}
	<table class="form">
		<tr><td class="label">{label for='name'}</td><td class="value">{input name='name'}</td></tr>
		<tr><td class="label">{label for='state'}</td><td class="value">{input name='state'}</td></tr>
		<tr><td class="label">{label for='city'}</td><td class="value">{input name='city'}</td></tr>
		<tr><td class="label">{label for='street'}</td><td class="value">{input name='street'}</td></tr>
		<tr><td class="label">{label for='zip'}</td><td class="value">{input name='zip'}</td></tr>
		<tr><td class="label">{label for='email'}</td><td class="value">{input name='email'}</td></tr>
	</table><br />
	<p align="center">{button action='checkout'}</p>
	{/form}
{/block}
</div>
{/container}