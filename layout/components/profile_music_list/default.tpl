{container stylesheet="profile_music_list.style"}

{block title=%title}
	<ul class="music_list">
	{foreach from=$music item='music_item'}
		<li class="list_item">
			<div class="music_info">
				<div class="small">
					{rate rate=$music_item.rate_score feature='music'}
					{$music_item.upload_stamp|spec_date}<br/>
				</div>
				<div class="small">
					{text %views} <span class="highlight">{$music_item.view_count}</span><br />
					{text %comments} <span class="highlight">{$music_item.comment_count}</span><br />
					{text %rates} <span class="highlight">{$music_item.rate_count}</span><br />
					<hr />
					{text %privacy} {text %stat_`$music_item.privacy_status`}<br />{text %status} {$music_item.status}
				</div>
			</div>
			<div class="music_thumb">
				<a href="{$music_item.music_page}">
					{if $music_item.thumb_img eq 'default'}
						<div class="music_def_thumb"></div>
					{else}
						<img src="{$music_item.thumb_img}" class="music_thumb" align="left" />
					{/if}
				</a><br clear="all" />
				<div class="small center">{component MusicControls music_id=$music_item.music_id}</div>
			</div>
			<div class="music_body">
				<a href="{$music_item.music_page}">{$music_item.title|out_format|smile|censor:"music":true}</a>
				<p>{$music_item.description|out_format|smile|censor:"music"}</p>
			</div>
			<div class="clr"></div>
		</li>
	{/foreach}
	</ul>
{/block}

{/container}
