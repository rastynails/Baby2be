{* Sign Up Link Component *}

{container stylesheet=sign_up_link.style}
	{block}
		<div class="link_container">
			<a href="{document_url doc_key=join_profile}">{text %link_label}</a>
		</div>
	{/block}
{/container}