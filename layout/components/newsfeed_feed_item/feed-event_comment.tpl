{username profile_id=$feed->actor_id assign=user}
{text %feeds.event_comment user=$user href=$feed->tpl_vars.url}