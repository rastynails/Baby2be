{username profile_id=$feed->actor_id assign=user}
<span class="feed_text">
	{text %feeds.music_upload user=$user href=$feed->tpl_vars.href title=$feed->tpl_vars.title|out_format|censor:'music':true}
</span>