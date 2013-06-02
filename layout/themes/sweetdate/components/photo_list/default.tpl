{* component PhotoList *}

{canvas}
	{container stylesheet="photo_list.style" class="photo_list"}
	{if $tabs}
			{menu type='tabs-small' items=$tabs}
	{/if}

	{paging total=$list.total on_page=$list.per_page pages=5}
		{block class="clearfix"}
		{block_cap}{/block_cap}
		<div class="clearfix">
		{foreach from=$list.items key="id" item="item"}
			<div class="photo_block">
				<div class="block">
					<div class="photo_cont float_left center">
						<a href="{$item.url}"><img title="{$item.title}" src="{$item.src}" width="100" height="100"/></a>
					</div>
					<div class="details_cont float_left">
						{rate rate=$rates[$id].avg_rate feature=photo}
						{if $comments !== false}
							<span class="small">{text %comments_label}: <span>{if $comments[$id]}<a href="{$item.url}">{$comments[$id]}</a>{else}0{/if}</span></span> <br/> 
						{/if}
						<span class="small">{text %views_label}: <span>{$item.viewed}</span></span> <br/> 
						<span class="small">{$item.added_stamp|spec_date}</span><br/>
						<span class="small photo_bot">{text %added_by_label}: <a href="{$item.owner_url}">{$item.owner_name|truncate:10:"...":true}</a></span><br/> 
					</div>
					<div class="clr_fix"></div>
				</div>
			</div>
		{/foreach}
		</div>
		{/block}
	{paging total=$list.total on_page=$list.per_page pages=5}
	{/container}
{/canvas}