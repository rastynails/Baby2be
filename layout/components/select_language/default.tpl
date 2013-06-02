{* Select Language Component *}

{container class="select_language"}
		{text %label}: 
		<select name="language_id" onchange="window.location.href=this.value;">
			{foreach from=$languages key=lang_id item=lang}
				<option value="{$lang->href}" {if $active_lang_id == $lang_id}selected="selected"{/if}>{$lang->label}</option>
			{/foreach}
		</select>
	
{/container}