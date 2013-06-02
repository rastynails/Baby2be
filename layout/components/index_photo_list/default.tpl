{* Component Photo List *}

{container stylesheet="index_photo_list.style" class="index_photo_list"}

{capture name=toolbar}
	<a href="javascript://" {id="view_more_btn"}>{text %view_more}</a>
{/capture}

{block}
{block_cap title=%title toolbar=$smarty.capture.toolbar}
	<div class="menu prototype_node">{menu type="block" items=$menu_items}</div>
{/block_cap}
<div class="photo_list_cont">
	<ul class="list prototype_node clearfix">
		<li class="item">
			<a href="javascript://" class="image"></a>
		</li>
	</ul>
</div>

<div class="empty_list_msg no_content" style="display:none">{text %no_items}</div>
{/block}	
{/container}