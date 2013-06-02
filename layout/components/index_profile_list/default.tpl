{* Component Profile List *}

{container stylesheet="index_profile_list.style" class="index_photo_list"}

{capture name=toolbar}
	<a href="javascript://" {id="view_more_btn"}>{text %view_more}</a>
{/capture}

{block}
{block_cap title=%title toolbar=$smarty.capture.toolbar}
	<div class="menu prototype_node">{menu type="block" items=$menu_items}</div>
{/block_cap}
<div class="profile_list_cont">
	<ul class="list prototype_node clearfix">
		<li class="item">
			<a href="javascript://" class="image profile_thumb_wrapper"></a>
			<div class="sex_line"></div>
			<a href="javascript://" class="label"></a>
		</li>
	</ul>
</div>

<div class="empty_list_msg no_content" style="display:none">{text %no_items}</div>
{/block}

{/container}