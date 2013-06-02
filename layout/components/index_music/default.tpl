{* Component IndexMusic *}

{container stylesheet="index_music.style"}
	{block}
	{if $service_msg}
	{block_cap title=%title}{/block_cap}
		<div class="no_content">{$service_msg}</div>
	{else}
	{block_cap title=%title}
		{menu type="ajax-block" items=$music_menu_items}
	{/block_cap}
	<div class="music_cont" {id="music_cont"}>
		{foreach from=$music item='list' key='list_name'}
		<div class="music_list" {id="music_`$list_name`"} style="display: {if $list.is_active}block;{else}none;{/if}">
			{if $music[$list_name].no_music}
				<div class="no_content">{$music[$list_name].no_music}</div>
			{else}
			<div class="block_toolbar"><a href="{$music[$list_name].view_more}">{text %more}</a></div>
			<div class="music_title"><a href="{$list.player.music_page}">{$list.player.title|out_format|censor:'music':true}</a></div>
			{if $list.player.music_source eq 'file'}
				{component MusPlayer music_file_url=$list.player.music_url width=$player_width height=$player_height}
			{else}
				{$list.player.code}<br />
			{/if}
			<br />
			<div class="music_thumb_list thumb_cont">
				{foreach from=$list.thumbs item='thumb'}
					<div class="music_thumb">
						{if $thumb.thumb_img eq 'default'}
							<a href="{$thumb.music_page}">{$thumb.title|out_format|truncate:15|censor:'music':true}</a>
							<a href="{$thumb.music_page}"><div class="music_def_thumb"></div></a>
						{elseif $thumb.thumb_img eq 'friends_only'}<div class="music_friends_thumb"></div><span class="a_fake">{$thumb.title|truncate:15|censor:'music':true}</span>
						{else}
							<a href="{$thumb.music_page}">{$thumb.title|out_format|truncate:15|censor:'music':true}</a>
							<a href="{$thumb.music_page}"><img src="{$thumb.thumb_img}" width="{$thumb_width}px" /></a>
						{/if}
					</div>
				{/foreach}
				<br clear="all" />
			</div>
			{/if}
		</div>
		{/foreach}
	</div>
	{/if}
	{/block}
{/container}

