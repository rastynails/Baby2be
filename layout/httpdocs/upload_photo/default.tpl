{*Http Document Upload Photo*}

{canvas}
	<div class="upload_photo_doc">
		{include_style file="upload_photo.style"}
		{if $tabs}
			{menu type="tabs-small" items=$tabs class="photo_tabs"}
		{/if}
		<div class="clr_div"></div>
		<div class="photo_content">
			{component $content_component}
		</div>
		
	</div>
{/canvas}