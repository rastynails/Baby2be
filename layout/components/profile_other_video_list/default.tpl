
{container}

{capture name='video_owner'}{text %video_from} {$owner_name}{/capture}
{capture name='more'}<a href="{$view_more_link}">{text %view_all}</a>{/capture}

	{block title=$smarty.capture.video_owner toolbar=$smarty.capture.more}
	{if $video}
		<table class="thumb_text_list">
		{foreach from=$video item='video_item'}
			<tr>
				<td class="thumb">
				<a href="{$video_item.video_page}">
					{if $video_item.thumb_img eq 'default'}
						<div class="video_def_thumb"></div>
					{elseif $video_item.thumb_img eq 'friends_only'}
						<div class="video_friends_thumb"></div>
				    {elseif $video_item.thumb_img == 'password_protected'}
				        <div class="video_password_thumb"></div>	
					{else}
						<img src="{$video_item.thumb_img}" class="video_thumb" align="left" />
					{/if}
				</a>
				</td>
				<td class="listing list_item">
					<a href="{$video_item.video_page}">{$video_item.title|out_format|smile|censor:"video":true}</a>
					<p class="small">{$video_item.upload_stamp|spec_date}<br />
					{text %views} <span class="highlight">{$video_item.view_count}</span><br />
					{rate rate=$video_item.rate_score feature='video'}
					</p>
				</td>
			</tr>
		{/foreach}
		</table>
	{else}
		<div class="no_content">{text %no_video}</div>
	{/if}
	{/block}
	
{/container}
