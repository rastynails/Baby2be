{* component Page Sidebar *}

{container stylesheet="sidebar.style" class="sidebar"}

	{if $sign_in}
		{component SignUpLink}
		{component SignIn}
	{/if}

	{component MemberConsole}
	
	{component ChuppoUseron}	
	
	{capture name=ads}{strip}
		{ads pos='sidebar'}
	{/strip}{/capture}
	
	{if $smarty.capture.ads}
		{block title=%.profile.list.ads_label}
		{$smarty.capture.ads}
		{/block}
	{/if}
	
	<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FBaby2be.dk%2F&amp;send=false&amp;layout=button_count&amp;width=130&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:130px; height:21px;" allowTransparency="true"></iframe>

	{* component HotList *}

{/container}
