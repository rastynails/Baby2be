{canvas}
	{container}
	{if !$service_msg}
	<br />
		<div class="float_half_left wide">
		{block title=$music_info.title|smile|censor:"music":true|out_format}
		<div class="mus_box">
			<div class="center" id="music_player_cont">
				{if $music_info.music_source eq 'file'}
					{component MusPlayer music_file_url=$music_info.music_url}
				{else}
					{$music_info.code}
				{/if}
			</div>
		</div>
		{/block}
		{component $MusicRate}
		{if $music_comments}{component $music_comments}{/if}
		
		</div>
		<div class="float_half_right narrow">
		
			{block title=%details}
				<table class="thumb_text_list">
					<tr>
						<td class="thumb">
							{profile_thumb profile_id=$music_info.profile_id size=60}
						</td>
						<td class="listing">
						<p class="small">{text %upload_by} <a href="{document_url doc_key='profile' profile_id=$music_info.profile_id}">{$music_info.owner_name}</a><br />
						{$music_info.upload_stamp|spec_date}<br />{text %.components.music_list.views} {$music_info.view_count}
						</td>
					</tr>
				</table>
				<p class="small">{text %descr}</p>
				<p>{$music_info.description|out_format:"comment"|smile|censor:"music"}</p>
				<div class="block_submit right">
					{*component Report type='video' reporter_id=$viewer_id entity_id=$video_info.video_id*}
				</div>
			{/block}
			{if $show_details}
				{block title=%share_details}
				<p class="all_row_width">
					{text %.components.music_view.embeddable_player}
					<input type="text" {id="video_code"} value="{$music_info.share_code}" />
					{text %permalink}
					<input type="text" {id="permalink"} value="{$music_info.permalink}" />
				</p>
				{/block}
			{/if}
			
			{component ProfileOtherMusicList profile_id=$music_info.profile_id except_music=$music_info.music_id}
		</div>	
		{else}
			<br /><div class="no_content">{$service_msg}</div>
		{/if}
		
	{/container}
{/canvas}