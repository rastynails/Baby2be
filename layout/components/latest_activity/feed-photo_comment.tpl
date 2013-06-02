{username profile_id=$feed->actor_id assign=user}
{text %feeds.photo_comment user=$user href=$feed->tpl_vars.photo_url}