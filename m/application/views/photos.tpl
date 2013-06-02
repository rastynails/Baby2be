
{block_cap}{$photo_sect}{/block_cap}
{block}
	{foreach from=$photos item='photo'}
		<a href="{$photo_url}{$photo.photo_id}"><img src='{$photo.thumb_url}' width='60' /></a>
	{/foreach}
{/block}
