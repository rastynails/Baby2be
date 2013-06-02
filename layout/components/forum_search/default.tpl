{* component Forum Search *}
{container stylesheet="forum_search.style"}

<input {id="search_btn"} type="button"  value="{text %forum_search}"/>
 
{* Forum Search Thickbox *}
<div style="display: none;">
	<div class="forum_search_title"><b>{text %labels.search_title}</b></div>
	<div class="forum_search_content">			
	{form ForumSearch}
		<table class="form">
			<tr>
				<td class="label">{label for="forum_search"}</td>
				<td class="value">{input name="forum_search" class="search_key"}</td>
			</tr>
			<tr>
				<td class="label">{label for="search_in_forums"}</td>
				<td class="value">{input name="search_in_forums" multiple="multiple" size="10" class="search_in"}</td>
			</tr>
		    <tr><td colspan="2" class="submit">{button action="search"}</td></tr>
		 </table>
 	{/form}
	</div>
</div>	
        
{/container}
