{* component Cls Index Categories*}

{container stylesheet='cls_index_categories.style'}

{if $profile_id && $showButton }
<div class="center">
	<input type="button" value="{text %`$entity`_post_btn}" onclick="window.location.href='{document_url doc_key=classifieds_new_item type=$entity}'"/>
</div>
<br/>
{/if}

{block title=%.components.cls.`$entity`}
	{$groups_list}
{/block}

{/container}