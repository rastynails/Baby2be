{* component 123Chat *}

{canvas}

{container}
{if !empty($dissalowd_msg)}
		{$dissalowd_msg}
	{else}
	<iframe src="{$chatUrl}" width="100%" height="700px">
	</iframe>
{/if}
{/container}

{/canvas}
