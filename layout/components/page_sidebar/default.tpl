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
	
	{component HotList}

{/container}
