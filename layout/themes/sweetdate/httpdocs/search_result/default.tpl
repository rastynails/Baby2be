{* HttpDoc Search Rsult *}

{canvas}

	<div class="float_left left_search_res">
		<a href="{document_url doc_key='profile_search' refine=1}">{text %refine}</a>
		{component SaveSearch}
	</div>
	<div class="float_right">
		<div class="saved_search_res">{component SavedLists}</div>
	</div>
	<div class="clr_div"></div>
	{component SearchResultList}
{/canvas}