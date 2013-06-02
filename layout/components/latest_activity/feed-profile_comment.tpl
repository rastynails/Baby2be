{assign value=$feed->tpl_vars var='vars'}
{username profile_id=$feed->actor_id assign=user}
{username profile_id=$vars.userId assign=user2}
{text %feeds.profile_comment user=$user user2=$user2}