{* friend_add user activity feed *}

<span class="feed_text">
	{username profile_id=$feed->actor_id} {$feed->user_status|truncate|out_format:40|smile|censor:'comment'}
</span>
