{* Sign Up Link Component *}

{container stylesheet=sign_up_link.style}
<div class="sign_up_wrap">
	{block class="block"}
	{block_cap}{/block_cap}
		<div class="link_container">
			<a href="{document_url doc_key=join_profile}">{text %link_label}</a>
		</div>
	{/block}
</div>
{/container}