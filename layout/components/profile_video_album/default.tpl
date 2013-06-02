{* Component ProfileVideoAlbum *}

{container stylesheet="profile_video_album.style"}

	{block_cap title=%title}
		<a class="delete_cmp" href="javascript://"></a>{menu type="ajax-block" items=$video_menu_items}
	{/block_cap}
    
	{block}
	{if $service_msg}
		<div class="no_content">{$service_msg}</div>
	{else}
		<div class="center">
		
		<div {id="video_latest"}>
		{if $first.video_id}
			{if $latest_friends_only}
				<div class="no_content">{$latest_friends_only}</div>
			{else}
                            {if $latest_locked}
                                <div {id="latest_unlock"} class="center">
                                    <div class="video_password_thumb" style="margin: 30px auto"></div>
                                    <form>
                                        <input type="password" name="password" />
                                        <input type="submit" value="{text %.components.photo_view.unlock}" />
                                    </form>
                                </div>
                            {else}
				<div class="video_title"><a href="{$first.video_page}">{$first.title|smile|out_format|censor:"video":true}</a></div>
				{if $first.video_source eq 'file'}
					{component VideoPlayer video_file_url=$first.video_url width=$player_width height=$player_height}
				{else}
					{$first.code}
				{/if}
                            {/if}
			{/if}
			<br /><br />
			<div class="video_thumb_list">
				{foreach from=$others item='thumb'}
					<div class="video_thumb">
						{if $thumb.thumb_img eq 'default'}
							<a href="{$thumb.video_page}"><div class="video_def_thumb"></div></a>
							<a href="{$thumb.video_page}">{$thumb.title|out_format|truncate:15|smile|censor:"video":true}</a>
						{elseif $thumb.thumb_img eq 'friends_only'}<div class="video_friends_thumb"></div><span class="a_fake">{$thumb.title|out_format|truncate:15|smile|censor:"video":true}</span>
						{elseif $thumb.thumb_img eq 'password_protected'}
						    <a href="{$thumb.video_page}"><div class="video_password_thumb"></div></a>
						    <a href="{$thumb.video_page}">{$thumb.title|out_format|truncate:15|smile|censor:"video":true}</a>
						{else}
							<a href="{$thumb.video_page}"><img src="{$thumb.thumb_img}" width="{$thumb_width}px" /></a>
							<a href="{$thumb.video_page}">{$thumb.title|out_format|truncate:15|smile|censor:"video":true}</a>
						{/if}
					</div>
				{/foreach}
				<br clear="all" />
			</div>
		{else}
			<div class="no_content">{text %no_video}</div>
		{/if}
		</div>
		
		<div {id="video_toprated"}  style="display: none;">
		{if $first_top.video_id}
			{if $top_friends_only}
				<div class="no_content">{$top_friends_only}</div>
			{else}
                            {if $top_locked}
                                <div {id="top_unlock"} class="center">
                                    <div class="video_password_thumb" style="margin: 30px auto"></div>
                                    <form>
                                        <input type="password" name="password" />
                                        <input type="submit" value="{text %.components.photo_view.unlock}" />
                                    </form>
                                </div>
                            {else}
				<div class="video_title"><a href="{$first_top.video_page}">{$first_top.title|out_format|smile|censor:"video":true}</a></div>
				{if $first_top.video_source eq 'file'}
					{component VideoPlayer video_file_url=$first_top.video_url width=$player_width height=$player_height}
				{else}
					{$first_top.code}
				{/if}
                            {/if}
			{/if}
			<br /><br />
			<div class="video_thumb_list">
				{foreach from=$others_top item='thumb'}
					<div class="video_thumb">
						{if $thumb.thumb_img eq 'default'}
							<a href="{$thumb.video_page}"><div class="video_def_thumb"></div></a>
							<a href="{$thumb.video_page}">{$thumb.title|out_format|truncate:15|smile|censor:"video":true}</a>
						{elseif $thumb.thumb_img eq 'friends_only'}<div class="video_friends_thumb"></div><span class="a_fake">{$thumb.title|out_format|truncate:15|censor:"video":true}</span>
						{else}
							<a href="{$thumb.video_page}"><img src="{$thumb.thumb_img}" width="{$thumb_width}px" /></a>
							<a href="{$thumb.video_page}">{$thumb.title|out_format|truncate:15|smile|censor:"video":true}</a>
						{/if}
					</div>
				{/foreach}
				<br clear="all" />
			</div>
		{else}
			<div class="no_content">{text %no_video}</div>
		{/if}		
		</div>
		</div>
	{/if}
	{/block}
{/container}