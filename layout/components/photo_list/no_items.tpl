{canvas}
	{container}
		{if $tabs}
				{menu type='tabs-small' items=$tabs}
		{/if}
		<br />
		<div class="no_content">
			{text %no_items}
		</div>
	{/container}
{/canvas}