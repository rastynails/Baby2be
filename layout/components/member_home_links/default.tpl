
{container class="memberhome_hrefs_cont" stylesheet="member_home_links.style"}
    
    {capture name='username'}
        {text %.components.member_home.label_welcome username=$username}
    {/capture}
    {block title=$smarty.capture.username}
    <div class="clearfix">
		{memberhome_href unit=match class="my_match"}
		{memberhome_href unit=mailbox class="mailbox"}
		{memberhome_href unit=bookmark class="bookmarks"}
		{memberhome_href unit=blocklist class="block_list"}
		{memberhome_href unit=friendlist class="friend-list"}
		{memberhome_href unit=new_members_list class="new_members"}
		{memberhome_href unit=who_checked_me_out class="who_view"}
		{memberhome_href unit=moderation_list class="moderator"}
        </div>
    {/block}
    
{/container}