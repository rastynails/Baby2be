{* component Chuppo Im *}

{canvas blank}

{container}

{if !$no_permission}
	<div style="height:550px; width:450px;" id="chuppo_im_cont" ></div>
{else}
	{block}<div class="no_content">{$no_permission}</div>{/block}
{/if}	

{/container}

{/canvas}
