{canvas}
{container stylesheet="styles.style"}
    <div class="container_block">
	    {block title=%pp_title}
			{form PhotoAlbumAccess}
			   <div style="display: none">{label %password_label for=password}</div>
			   <div class="block_info">
			     {$title|censor:'photo':true}
			   </div>
			   <div class="clearfix content_c">
				   <div class="input_c">{input name=password}</div>
				   <div class="button_c">{button action=unlock}</div>
			   </div>
			{/form}
		{/block}
	</div>
{/container}
{/canvas}