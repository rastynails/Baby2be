{container}
{block_cap title=$test.info.question}{/block_cap}
{block}
<form id="poll" method="post">
<input type="hidden" name="command" value="test_pass" />
	<table>
	{foreach from=$test.answers item='answer'}
		<tr>
			<td>
				<input type="radio" name="test[{$test.info.id}]" value="{$answer.id}"/>
			</td>
			<td>
				{$answer.answer}
			</td>
		</tr>
	{/foreach}
		<tr>
			<td colspan="2" align="right">
				<input type="submit" value="OK"/>
			</td>
		</tr>
	</table>
</form>
{/block}
{/container}