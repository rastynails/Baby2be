{* HttpDoc Search Rsult *}

{canvas}
	<div class="clearfix">
	<div class="float_right search_menu_wrap">
		<table class="search_menu" cellpadding="0" cellspacing="0">
			<tr>
				<td><a class="refine_search" href="{document_url doc_key='profile_search' refine=1}">{text %refine}</a></td>
				<td>{component SaveSearch}</td>
			</tr>
		</table>
	</div>	


	<div class="float_left">
		{component SavedLists}
	</div>
	<div class="clr_div"></div>
	</div>
	{component SearchResultList}
{/canvas}