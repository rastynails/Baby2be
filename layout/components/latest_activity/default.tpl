{* component Latest Activity *}

{container stylesheet="latest_activity.style"}
	{block}
	{block_cap title=%latest_activity}
		<a class="delete_cmp" href="javascript://"></a>
	{/block_cap}	
	<div class="latest_activity_container">
	{foreach from=$activity_list item=day}
		<div class="feed_day_cap">{$day->date}</div>
		<div class="feed_day">
		{foreach from=$day->feeds item=feed}
			<div class="feed feed-{$feed->type}">
				<div class="feed_time">{$feed->time}</div>
				<div class="feed_body">
					{include file="`$this->tpl_dir`feed-`$feed->type`.tpl"}
				</div>
			</div>
		{/foreach}
		</div>
	{/foreach}
    </div>
    <div class="latest_activity_view_more_c" style="text-align: center;">
        <input type="button" class="latest_activity_view_more" value="{text %view_more}">
    </div>
	{/block}
{/container}