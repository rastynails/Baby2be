{canvas}
	{container stylesheet='photo_albums.style'}
		{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
			<ul class="list">
				{foreach from=$items item=item}
					<li class="item">
						<div class="thumb">
							<a href="{$item->getUrl($edit_mode)}"><img src="{$item->getThumb_url()}"/></a>
						</div>
						<div class="label">
							<a href="{$item->getUrl($edit_mode)}">{$item->getView_label()|censor:'photo'}</a>
						</div>
					</li>
				{foreachelse}
					<li class="no_content">
						{text %no_items}
					</li>	
				{/foreach}
			</ul>
			<br clear="all" />
		{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
	{/container}
{/canvas}