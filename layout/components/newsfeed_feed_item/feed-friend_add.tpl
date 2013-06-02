{* friend_add user action feed *}

	<div class="list_cont">
		<ul class="list">
			<li class="item">
                {profile_thumb profile_id=$item.content.profile_id size=50}
			</li>
            <li class="item">
                {profile_thumb profile_id=$item.content.friend_id size=50}
            </li>
		</ul>
	</div>

