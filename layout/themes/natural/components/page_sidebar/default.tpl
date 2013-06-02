{* component Page Sidebar *}

{container stylesheet="sidebar.style" class="sidebar"}
<div class="sidebar_top"><div class="sidebar_top_r"><div class="sidebar_top_c">&nbsp;</div></div></div>
 <div class="sidebar_cont">
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
    
   </div>
<div class="sidebar_bot"><div class="sidebar_bot_r"><div class="sidebar_bot_c">&nbsp;</div></div></div>
{/container}
