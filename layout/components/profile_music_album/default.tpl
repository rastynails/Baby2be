{* Component ProfileMusicAlbum *}

{container stylesheet="profile_music_album.style"}

	{block_cap title=%title}
		<a class="delete_cmp" href="javascript://"></a>{menu type="ajax-block" items=$music_menu_items}
	{/block_cap}
    
	{block}
	{if $service_msg}
		<div class="no_content">{$service_msg}</div>
	{else}
		<div class="center">
		
		<div {id="music_latest"}>
		{if $first.music_id}
			{if $latest_friends_only}
				<div class="no_content">{$latest_friends_only}</div>
			{else}
				<div class="music_title"><a href="{$first.music_page}">{$first.title|out_format|smile|censor:"music":true}</a></div>
				{if $first.music_source eq 'file'}
					{component MusPlayer music_file_url=$first.music_url width=$player_width height=$player_height}
				{else}
					{$first.code}
				{/if}
			{/if}
			<br /><br />
			<div class="music_thumb_list clearfix">
				{foreach from=$others item='thumb'}
					<div class="music_thumb">
						{if $thumb.thumb_img eq 'default'}
							<a href="{$thumb.music_page}"><div class="music_def_thumb"></div></a>
							<a href="{$thumb.music_page}">{$thumb.title|out_format|truncate:15|smile|censor:"music":true}</a>
						{elseif $thumb.thumb_img eq 'friends_only'}<div class="music_friends_thumb"></div><span class="a_fake">{$thumb.title|out_format|truncate:15|censor:"music"}</span>
						{else}
							<a href="{$thumb.music_page}"><img src="{$thumb.thumb_img}" width="{$thumb_width}px" /></a>
							<a href="{$thumb.music_page}">{$thumb.title|out_format|truncate:15|smile|censor:"music":true}</a>
						{/if}
					</div>
				{/foreach}
				<br clear="all" />
			</div>
		{else}
			<div class="no_content">{text %no_music}</div>
		{/if}
		</div>
		
		<div {id="music_toprated"}  style="display: none;">
		{if $first_top.music_id}
			{if $top_friends_only}
				<div class="no_content">{$top_friends_only}</div>
			{else}
				<div class="music_title"><a href="{$first_top.music_page}">{$first_top.title|out_format|smile|censor:"music":true}</a></div>
				{if $first_top.music_source eq 'file'}
					{component MusPlayer music_file_url=$first_top.music_url width=$player_width height=$player_height}
				{else}
					{$first_top.code}
				{/if}
			{/if}
			<br /><br />
			<div class="music_thumb_list">
				{foreach from=$others_top item='thumb'}
					<div class="music_thumb">
						{if $thumb.thumb_img eq 'default'}
							<a href="{$thumb.music_page}"><div class="music_def_thumb"></div></a>
							<a href="{$thumb.music_page}">{$thumb.title|out_format|truncate:15|smile|censor:"music":true}</a>
						{elseif $thumb.thumb_img eq 'friends_only'}<div class="music_friends_thumb"></div><span class="a_fake">{$thumb.title|out_format|truncate:15|censor:"music":true}</span>
						{else}
							<a href="{$thumb.music_page}"><img src="{$thumb.thumb_img}" width="{$thumb_width}px" /></a>
							<a href="{$thumb.music_page}">{$thumb.title|out_format|truncate:15|smile|censor:"music":true}</a>
						{/if}
					</div>
				{/foreach}
				<br clear="all" />
			</div>
		{else}
			<div class="no_content">{text %no_music}</div>
		{/if}		
		</div>
		</div>
	{/if}
	{/block}
{/container}