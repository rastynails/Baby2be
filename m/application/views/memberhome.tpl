
{block}
	<div class="highlight">{$welcome}</div>
{/block}

{block_cap}{text section='mobile' key='browse_members'}{/block_cap}
{block}
<ul class="item_list">
{if $latest.total && $latest_enabled}
<li>
	<a href="{$members_url}new">{text section='nav_menu_item' key='new_members'}</a> ({$latest.total})
	<div>
		{foreach from=$latest.list item='p'}
			{profile_thumb profile_id=$p->profile_id size=60}
		{/foreach}
		<div class="small"><a href="{$members_url}new">{text section='mobile' key='view_more'} &raquo;</a></div>
	</div>
</li>
{/if}

{if $online.total && $online_enabled}
<li>
	<a href="{$members_url}online">{text section='nav_menu_item' key='online_list'}</a> ({$online.total})
	<div>
		{foreach from=$online.list item='p'}
			{profile_thumb profile_id=$p->profile_id size=60}
		{/foreach}
		<br />
		<div class="small"><a href="{$members_url}online">{text section='mobile' key='view_more'} &raquo;</a></div>
	</div>
</li>
{/if}

{if $matches.total && $matches_enabled}
<li>
	<a href="{$members_url}matches">{text section='nav_doc_item' key='match_list'}</a> ({$matches.total})
	<div>
		{foreach from=$matches.list item='p'}
			<a href="{$profile_url}{$p->username}">{profile_thumb profile_id=$p->profile_id size=60}</a>
		{/foreach}
		<div class="small"><a href="{$members_url}matches">{text section='mobile' key='view_more'} &raquo;</a></div>
	</div>
</li>
{/if}

{if $bookmarks.total && $bookmarks_enabled}
<li>
	<a href="{$members_url}bookmarks">{text section='memberhome' key='href_my_hotlist'}</a> ({$bookmarks.total})
</li>
{/if}
</ul>
{/block}