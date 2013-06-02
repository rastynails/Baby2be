{username profile_id=$feed->actor_id assign=user}
<span class="feed_text">
	{text %feeds.media_upload user=$user href=$feed->tpl_vars.href title=$feed->tpl_vars.title|out_format|censor:'video':true description=$feed->tpl_vars.description|out_format|censor:'video'}
</span>