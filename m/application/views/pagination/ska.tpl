

<p class="paging">
	{if $total_pages > 1}
	
	{if $previous_page}
		<a href="{$url|replace:'(page)':$previous_page}">{text section='navigation.paging' key=prev_page}</a>
	{*else}
		<span class="curr">{text section='navigation.paging' key=prev_page}</span>*}
	{/if}

	{* << Previous  1 2 3 4 5 6 7 8 9 10 11 12  Next >> *}
	{if $total_pages < 13}
		{section name='i' start=1 loop=$total_pages} 
			{if $smarty.section.i.index == $current_page}
				<span class="curr active">{$smarty.section.i.index}</span>
			{else}
				<a href="{$url|replace:'(page)':$smarty.section.i.index}">{$smarty.section.i.index}</a>
			{/if}
		{/section}

	{* << Previous  1 2 3 4 5 6 7 8 9 10 ... 25 26  Next >> *}
	{elseif $current_page < 9}
		{section name='i' start=1 loop=10}
			{if $smarty.section.i.index == $current_page}
				<span class="curr active">{$smarty.section.i.index}</span>
			{else}
				<a href="{$url|replace:'(page)':$smarty.section.i.index}">{$smarty.section.i.index}</a>
			{/if}
		{/section}
		
		{assign var="prev" value=$total_pages-1}
		<span class="curr">&hellip;</span>
		<a href="{$url|replace:'(page)':$prev}">{$prev}</a>
		<a href="{$url|replace:'(page)':$total_pages}">{$total_pages}</a>

	{* << Previous  1 2 ... 17 18 19 20 21 22 23 24 25 26  Next >> *}
	{elseif $current_page > $total_pages - 8}
		<a href="{$url|replace:'(page)':1}">1</a>
		<a href="{$url|replace:'(page)':2}">2</a>
		<span class="curr">&hellip;</span>

		{capture name=from}
			{math equation="x-y" x = $total_pages y = 9}
		{/capture}
		
		{capture name=to}
			{math equation="x+y" x = $total_pages y = 1}
		{/capture}

		{section name='i' start=$smarty.capture.from loop=$smarty.capture.to}
			{if $smarty.section.i.index == $current_page}
				{if $next_page}<span class="curr active">{$smarty.section.i.index}</span>{/if}
			{else}
				<a href="{$url|replace:'(page)':$smarty.section.i.index}">{$smarty.section.i.index}</a>
			{/if}
		{/section}

	{* << Previous  1 2 ... 5 6 7 8 9 10 11 12 13 14 ... 25 26  Next >> *}
	{else}
		<a href="{$url|replace:'(page)':1}">1</a>
		<a href="{$url|replace:'(page)':2}">2</a>
		<span class="curr">&hellip;</span>
		
		{capture name=from}
			{math equation="x-y" x = $current_page y = 5}
		{/capture}
		
		{capture name=to}
			{math equation="x+y" x = $current_page y = 5}
		{/capture}
		
		{section name='i' start=$smarty.capture.from loop=$smarty.capture.to}
			{if $smarty.section.i.index == $current_page}
				<span class="curr active">{$smarty.section.i.index}</span>
			{else}
				<a href="{$url|replace:'(page)':$smarty.section.i.index}">{$smarty.section.i.index}</a>
			{/if}
		{/section}
		
		{assign var="prev" value=$total_pages-1}
		<span class="curr">&hellip;</span>
		<a href="{$url|replace:'(page)':$prev}">{$prev}</a>
		<a href="{$url|replace:'(page)':$total_pages}">{$total_pages}</a>
	{/if}
	
	{if $next_page}
		<a href="{$url|replace:'(page)':$next_page}">{text section='navigation.paging' key=next_page}</a>
	{else}
		<span class="curr active">{$current_page}</span>
	{/if}
	{/if}
<br clear="all" />
</p>