
{container}

{capture name='music_owner'}{text %music_from} {$owner_name}{/capture}
{capture name='more'}<a href="{$view_more_link}">{text %view_all}</a>{/capture}

	{block title=$smarty.capture.music_owner toolbar=$smarty.capture.more}
	{if $music}
		<table class="thumb_text_list">
		{foreach from=$music item='music_item'}
			<tr>
				<td class="thumb">
				<a href="{$music_item.music_page}">
					{if $music_item.thumb_img eq 'default'}
						<div class="music_def_thumb"></div>
					{elseif $music_item.thumb_img eq 'friends_only'}
						<div class="music_friends_thumb"></div>
					{else}
						<img src="{$music_item.thumb_img}" class="video_thumb" align="left" />
					{/if}
				</a>
				</td>
				<td class="listing list_item">
					<a href="{$music_item.music_page}">{$music_item.title|smile|out_format|censor:"music":true}</a>
					<p class="small">{$music_item.upload_stamp|spec_date}<br />
					{text %views} <span class="highlight">{$music_item.view_count}</span><br />
					{rate rate=$music_item.rate_score feature='music'}
					</p>
				</td>
			</tr>
		{/foreach}
		</table>
	{else}
		<div class="no_content">{text %no_music}</div>
	{/if}
	{/block}
	
{/container}
