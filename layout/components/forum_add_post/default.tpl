{* component Forum Add Post *}

{container}

{if !$no_permission}
<div class="wide automargin">
	{block title=%labels.new_post}
		{form ForumAddPost}
		<table class="form">
			<tr>
				<td>
				    {text_formatter for='post_text' entity="forum"}
				    {input name="post_text" class="area_big"}
				</td>
			</tr>
			<tr>
                <td class="value">
                    <div style="font-weight: bold; padding: 2px;">{label for="attachment"}:</div>
                    {input name="attachment"}
                </td>
            </tr>       
 			<tr>
 				<td class="submit">{button action="post"}</td>
 			</tr>			
		 </table>		 
		 
		 {/form}
	{/block}
</div>
{else}
	<div class="no_content">{$no_permission}</div>
{/if}

{/container}

