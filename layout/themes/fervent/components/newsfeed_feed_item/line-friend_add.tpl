{capture name=profile_url}
    {profile_username profile_id=$item.content.profile_id}
{/capture}

{capture name=friend_url}
    {profile_username profile_id=$item.content.friend_id}
{/capture}

{text %feeds.friend_add profile_url=$smarty.capture.profile_url friend_url=$smarty.capture.friend_url}