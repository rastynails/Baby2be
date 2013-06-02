{* component Forum Add Topic *}

{container stylesheet='forum_add_topic.style'}

{if !$no_permission}
	<div class="wider automargin">
		{block title=%labels.new_topic}
			{form ForumAddTopic}
			<table class="form">
				<tr>
					<td class="label">{label for="title"}</td>
					<td class="value all_row_width">{input name="title"}</td>
				</tr>
				<tr>
					<td class="label">{label for="first_post"}</td>
					<td class="value">
					    {text_formatter for='first_post' entity="forum"}
						{input name="first_post" class="area_big"}
					</td>
				</tr>
	            <tr>
				<tr>
					<td class="label">{label for="notify_me"}</td>
					<td class="value">{input name="notify_me"}</td>
				</tr>
				<tr>
	                <td class="label">{label for="attachment"}</td>
	                <td class="value">{input name="attachment"}</td>
	            </tr>		
				
			    <tr><td colspan="2" class="submit">{button action="post"}</td></tr>
			 </table>
			 {/form}
		{/block}
	</div>
{else}
	<div class="no_content">{$no_permission}</div>
{/if}

{/container}

