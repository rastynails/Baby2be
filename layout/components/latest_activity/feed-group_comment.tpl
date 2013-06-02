{username profile_id=$feed->actor_id assign=user}
{text %feeds.group_comment user=$user href=$feed->tpl_vars.group_url title=$feed->tpl_vars.title|censor:'group':true}