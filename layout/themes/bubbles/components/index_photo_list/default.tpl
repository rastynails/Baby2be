{* Component Photo List *}

{container stylesheet="index_photo_list.style" class="index_photo_list"}

{block}
{block_cap title=%title}{/block_cap}
<div class="photo_list_cont">
	<ul class="list prototype_node">
		<li class="item">
			<a href="javascript://" class="image"><div class="index_frame"></div></a>
		</li>
	</ul>
	<a class="index_more" title="{text %view_more}" href="javascript://" {id="view_more_btn"}></a>
	<div class="clr"></div>
</div>

<div class="empty_list_msg no_content" style="display:none">{text %no_items}</div>
<div class="menu prototype_node">{menu type="block" items=$menu_items}</div>
{/block}	
{/container}