{* component Chuppo Chat *}

{canvas}

{container}

{if !$no_permission}
	<div style="height: 600px;" id="chuppo_chat_cont" ></div>
{else}
	<div class="no_content">{$no_permission}</div>
{/if}

{/container}

{/canvas}
