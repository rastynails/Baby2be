{canvas}
	{container}
	
{literal}
<style>

.choice{
	background-color: #CC4201;
	border-right: 1px solid #CC4201;  
}

.selected-choice{
	background-color: #0063D1;
	border-right: 1px solid #0063D1;
}
table tr td{
	padding: 3px;
}	
</style>	
{/literal}	
		<div style="">
			{if $poll}
			{assign var='full' value=100}
				{block_cap title=$poll.question}{/block_cap}
				{block}
				<table>
				{foreach from=$poll.answers item='ans'}
					<tr valign="middle">
						<td><b>{$ans.answer}</b></td> 
						<td>
							<div class="{if $poll.my_choice.answerId eq $ans.id }selected-choice{else}choice{/if}" style="width: {$full/100*$ans.percent}px;">&nbsp;</div></div>
						</td>
						<td align="right">
							&nbsp;&nbsp;<b>{$ans.percent}%</b>
						</td>
					</tr>
				{/foreach}
					<tr>
						<td colspan="3">
							<br />
							&nbsp;&nbsp;&nbsp;{text %.polls.total_votes_label}: {$poll.votes_total}
						</td>
					</tr>
				</table>
				{/block}
				<br />
			{/if}
		</div>	
		{foreach from=$polls item="_poll"}
			{if $_poll.my_choice.answerId && $_poll.my_choice.answerId != $poll.my_choice.answerId}
				{block_cap title=$_poll.question}{/block_cap}
				{block}
				<table>
				{foreach from=$_poll.answers item='ans'}
					<tr valign="middle">
						<td><b>{$ans.answer}</b></td> 
						<td>
							<div class="{if $_poll.my_choice.answerId eq $ans.id }selected-choice{else}choice{/if}" style="width: {$ans.percent}px;">&nbsp;</div></div>
						</td>
						<td align="right">
							&nbsp;&nbsp;<b>{$ans.percent}%</b>
						</td>
					</tr>
				{/foreach}
					<tr>
						<td colspan="3">
							<br />
							&nbsp;&nbsp;&nbsp;{text %.polls.total_votes_label}: {$_poll.votes_total}
						</td>
					</tr>				
				</table>
				<br />
				{/block}
			{elseif !$_poll.my_choice.answerId }
				{component Poll pollId=$_poll.id}
			{/if}
		{/foreach}
	{/container}
{/canvas}