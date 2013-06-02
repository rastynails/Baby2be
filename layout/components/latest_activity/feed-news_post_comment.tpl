{username profile_id=$feed->actor_id assign=user}
{text %feeds.news_post_comment user=$user href=$feed->tpl_vars.href}