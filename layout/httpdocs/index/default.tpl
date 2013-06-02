{* MasterPage Index component *}

{canvas sidebar=no}
	{component PromoBox}
	<div class="float_half_left">
		<div class="quick_search_container">
			{component QuickSearch}
		</div>
		
		{component TotalUserStatistics}
		{component DashboardForumTopics}
	</div>
	
	<div class="float_half_right">
		{component MemberListBoard list='featured'}
		{component PhotoListBoard}
	</div>
{/canvas}
