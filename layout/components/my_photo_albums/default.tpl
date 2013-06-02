{canvas}
	{container stylesheet="my_photo_albums.style"}
			<div class="float_half_left left_part">
				{component PhotoAlbums edit_mode=true}
			</div>
			
			<div class="float_half_right right_part">
				{component NewAlbum}
			</div>
			<br clear="all" />
	{/container}
{/canvas}