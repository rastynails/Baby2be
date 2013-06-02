
{container stylesheet="profile_video_list.style"}

{block title=%title}
	<ul class="video_list">
	{foreach from=$video item='video_item'}
		<li class="list_item">
		{if $video_item.is_converted eq 'no'}
			<div class="video_info">
				<div class="small">{$video_item.upload_stamp|spec_date}<br/>{text %status} {text %not_converted}<br /></div>
			</div>
			<div class="video_thumb">
				<div class="video_def_thumb"></div>
			</div>
			<div class="video_body">
				<span class="a_fake">{$video_item.title|out_format|smile|censor:"video":true}</span>
				<p>{$video_item.description|out_format:"comment"|smile|censor:"video":true}</p>
			</div>
		{else}
			<div class="video_info">
				<div class="small">
					{rate rate=$video_item.rate_score feature='video'}
					{$video_item.upload_stamp|spec_date}<br/>
				</div>
				<div class="small">
					{text %views} <span class="highlight">{$video_item.view_count}</span><br />
					{text %comments} <span class="highlight">{$video_item.comment_count}</span><br />
					{text %rates} <span class="highlight">{$video_item.rate_count}</span><br />
					<hr />
					{text %privacy} {text %stat_`$video_item.privacy_status`}<br />{text %status} {text %status_`$video_item.status`}
				</div>
			</div>
			<div class="video_thumb">
				<a href="{$video_item.video_page}">
					{if $video_item.thumb_img eq 'default'}
						<div class="video_def_thumb"></div>
					{else}
						<img src="{$video_item.thumb_img}" class="video_thumb" align="left" />
					{/if}
				</a><br clear="all" />
				<div class="small center">{component VideoControls video_id=$video_item.video_id}</div>
			</div>
			<div class="video_body">
				<a href="{$video_item.video_page}">{$video_item.title|out_format|smile|censor:"video":true}</a>
				<p>{$video_item.description|out_format|smile|censor:"video":true}</p>
			</div>
		{/if}
			<div class="clr"></div>
		</li>
	{/foreach}
	</ul>
{/block}

{/container}
