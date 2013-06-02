{* Component Photo Gallery *}

{container stylesheet="photo_gallery.style" class="photo_gallery"}

<div style="display: none">
	<div {id="tooltip"} class="photo_info">
		<table width="200px" class="form">
			<tr>
				<td class="label">{text %title_label}</td><td class="title value"></td>
			</tr>
			<tr>
			<td class="label">{text %description_label}</td><td class="description value"></td>
			</tr>
		</table>
	</div>
</div>

{capture name=toolbar}
	<div class="toolbar" style="display:none">
		<a href="javascript://" class="fullsize_btn">{text %toolbar.full_s_btn}</a>
	</div>
{/capture}

{block title=%title toolbar=$smarty.capture.toolbar}

	<div class="preview">
		<div class="rate_container" style="display: none">
			{component $rate}
		</div>
		<div class="image">
		    {if $photo_ver_avaliable}
				<div class="photo_auth_mark" style="display: none; position: absolute; top: 10px; left: 10px" {id="auth_mark"}>
				     <img src="{$photo_auth_icon}" />
				</div>
		    {/if}
			<div class="password_cont" style="display: none">
				<form>
					<input type="password" name="password"><input type="submit" value="Unlock">
				</form>
			</div>
		</div>
	</div>
	<br clear="all" />
	<div class="carousel_cont preloader">
		<ul {id="carousel"} class="jcarousel-skin" style="display: none">
		</ul>
	</div>

{/block}


{/container}