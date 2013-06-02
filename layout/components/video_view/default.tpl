
{canvas}
	{container}
		<div class="float_half_left wide">
		{if $service_msg}
		  <div class="no_content">{$service_msg}</div>
		{elseif $pass_protection}
		  {block}
		  <div {id="password_unlock"} class="center">
		  <div class="video_password_thumb" style="margin: 30px auto"></div>
		  <form>
		      <input type="password" name="password" />
		      <input type="submit" value="{text %.components.photo_view.unlock}" />
		  </form>
		  </div>
		  {/block}
		{else}
    		{block title=$video_info.title|smile|censor:"video":true|out_format}
    		<div class="video_box">
    			<div class="center" id="video_player_cont">
    				{if $video_info.video_source eq 'file'}
    					{component VideoPlayer video_file_url=$video_info.video_url}
    				{else}
    					{$video_info.code}
    				{/if}
    			</div>
    		</div>
    		{/block}
                {component ContentSocialSharing}
    		{component $VideoRate}
    		{if $video_comments}{component $video_comments}{/if}
		{/if}
		</div>
		<div class="float_half_right narrow">
		{if !$service_msg && !$pass_protection}
			{block title=%details}
				<table class="thumb_text_list">
					<tr>
						<td class="thumb">
							{profile_thumb profile_id=$video_info.profile_id size=60}
						</td>
						<td class="listing">
						<p class="small">{text %upload_by} <a href="{document_url doc_key='profile' profile_id=$video_info.profile_id}">{$video_info.owner_name}</a><br />
                        {$video_info.upload_stamp|spec_date}<br />                        
                        {if $enable_categories && $video_info.category_id}{text %.components.video_list.category} {text %.video_categories.cat_`$video_info.category_id`}<br />{/if}						
                        {text %.components.video_list.views} {$video_info.view_count}
						</td>
					</tr>
				</table>
				<p class="small">{text %descr}</p>
				<p>{$video_info.description|out_format:"comment"|smile|censor:"video":true}</p>
				<div class="block_submit right">
					{component Report type='video' reporter_id=$viewer_id entity_id=$video_info.video_id}
				</div>
			{/block}
			{if $show_details}
				{block title=%share_details}
				<p class="all_row_width">
					{text %embeddable_player}
					<input type="text" {id="video_code"} value="{$video_info.share_code}" />
					{text %permalink}
					<input type="text" {id="permalink"} value="{$video_info.permalink}" />
				</p>
				{/block}
			{/if}
			{component $VideoTags}
		{/if}
		{component ProfileOtherVideoList profile_id=$video_info.profile_id except_video=$video_info.video_id}
		</div>
		
	{/container}
{/canvas}