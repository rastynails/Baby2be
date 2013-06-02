{* component Forum New Topic *}
{canvas}
{container class="centered_form" stylesheet="forum_new_topic.style"}

{if !$no_permission}
	{block title=%labels.new_topic}
		{form ForumNewTopic}
		<table class="form">
			<tr>
			{if isset($group_title)}
				<td class="label">{text %in_group}</td>
				<td class="value">{$group_title}</td>
			{else}
				<td class="label">{label for="forum"}</td>
				<td class="value">{input name="forum_id"}</td>
			{/if}	
			</tr>		
			<tr>
				<td class="label">{label for="title"}</td>
				<td class="value">{input name="title"}</td>
			</tr>
			<tr>
				<td class="label">{label for="first_post"}</td>
				<td class="value">
					{input name="first_post"}
				</td>
			</tr>
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
{else}
	<div class="no_content">{$no_permission}</div>
{/if}

{/container}
{/canvas}
