{* friend_add user activity feed *}

{username profile_id=$feed->actor_id assign=user1}

{if $feed->items_c > 1}
	<span class="feed_text">
		{text %feeds.friend_add_group user1=$user1 items_c=$feed->items_c}
	</span>
	<div class="feed_items">
		{foreach from=$feed->items item=profile_id}
			<div class="profile_item">
				{profile_thumb profile_id=$profile_id username=yes size=50}
			</div>
		{/foreach}
		<div class="clr"></div>
	</div>
{else}
	{username profile_id=$feed->items.0 assign=user2}
	<span class="feed_text">
		{text %feeds.friend_add user1=$user1 user2=$user2}
	</span>
{/if}
