
{canvas}
{container stylesheet="music_list.style"}

{if $list_type eq 'tags' && !$tag_words}
	{component $MusicTagNavigator}
{else}

{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
{capture name='list_title'}
	{if $list_type eq 'tags'}{text %tags_label} '{$tag_words}'{elseif $list_type eq 'profile'}{text %music_by} {$username}{else}{text %list.`$list_type`}{/if}
{/capture}

<div class="float_half_left wider">

{block title=$smarty.capture.list_title}
	<ul class="music_list">
	{foreach from=$list item=music'}
		<li class="list_item">
			<div class="music_info">
				<div class="small">
					{rate rate=$music.rate_score feature='music'}
					{text %by} <a href="{document_url doc_key='profile' profile_id=$music.profile_id}">{$music.username}</a>,
					<br />{$music.upload_stamp|spec_date}<br />
					{text %views} <span class="highlight">{$music.view_count}</span> <br />
					{text %comments} <span class="highlight">{$music.comment_count}</span> <br />
				</div>
			</div>
			<div class="music_thumb">
				{if $music.thumb_img eq 'default'}
					<a href="{$music.music_page}"><div class="music_def_thumb"></div></a>
				{elseif $music.thumb_img eq 'friends_only'}
					<div class="music_friends_thumb"></div>
				{else}
					<a href="{$music.music_page}"><img src="{$music.thumb_img}" class="music_thumb" align="left" /></a>
				{/if}
			</div>
			<div class="music_body">
				{if $music.thumb_img != 'friends_only'}<a href="{$music.music_page}">{$music.title|smile|out_format|censor:"music":true}</a>
				{else}
					<span class="a_fake">{$music.title|out_format|smile|censor:"music":true}</span>
				{/if}
				<p>{$music.description|truncate:150|smile|out_format|censor:"music"}</p>
			</div>
			<br clear="all" />
			<div class="clr"></div>
		</li>
	{/foreach}
	</ul>
{/block}
</div>

<div class="float_half_right narrower">
	{component $MusicTagNavigator}
</div>

<br clear="all" />
{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
{/if}
{/container}
{/canvas}