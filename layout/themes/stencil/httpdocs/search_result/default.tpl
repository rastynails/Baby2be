{* HttpDoc Search Rsult *}

{canvas}

	<div class="float_right search_result_right">
		<a class="refine_search" href="{document_url doc_key='profile_search' refine=1}">{text %refine}</a>
		{component SaveSearch}
	</div>
	<div class="float_left">
		{component SavedLists}
	</div>
	<br clear="all" />
	
	{component SearchResultList}
{/canvas}