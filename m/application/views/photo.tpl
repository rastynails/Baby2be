
{block_cap}{$title}{/block_cap}
{block}
	{if !$permission.avaliable}
		<div class="not_found">{$permission.msg}</div>
	{else}
	{if $photo.title}
		{text section='components.photo_gallery' key='title_label'}: {$photo.title}<br />
	{/if}
	
	{if $photo.html_description}
		{text section='components.photo_view.details' key='description_label'}:	{$photo.html_description}<br />
	{/if}
	
	{text section='components.photo_view.details' key=added}: <a href="{$photo.owner_url}">{$photo.owner_name|truncate:18:"...":true}</a>, <span class="small">{$photo.added|spec_date}</span><br />

	{text section='components.photo_view.details' key=views}: {$photo.views}
	<br /><br />	
	<img src="{$photo.src}" style="width: 100%" />
	
	<table width="100%" class="small">
	<tr>
		<td>
		{if isset($prev)}
			<a href="{$prev}">{text section='mobile' key='photo_prev'}</a>
		{/if}
		</td>
		<td align="right">
		{if isset($next)}
			<a href="{$next}">{text section='mobile' key='photo_next'}</a>
		{/if}
		</td>
	</tr>
	</table>
	{/if}
{/block}
