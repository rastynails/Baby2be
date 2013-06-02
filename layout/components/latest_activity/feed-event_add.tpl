{username profile_id=$feed->actor_id assign=user}
<span class="feed_text">
	{text %feeds.event_add user1=$user href=$feed->tpl_vars.event_url title=$feed->tpl_vars.title|censor:'event':true}
</span>