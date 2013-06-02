{* Component Profile List *}

{container stylesheet="index_profile_list.style" class="index_profile_list"}

{block}
{block_cap title=%title }{/block_cap}
<div class="profile_list_cont">
	<ul class="list prototype_node">
		<li class="item">
			<a href="javascript://" class="image profile_thumb_wrapper">
			<span class="label"></span>
			<div class="index_frame"></div>
			</a>
			<div class="sex_line"></div>
		</li>
	</ul>
	<a class="index_more" title="{text %view_more}" href="javascript://" {id="view_more_btn"}></a>
	<div class="clr"></div>
</div>

<div class="empty_list_msg no_content" style="display:none">{text %no_items}</div>

<div class="menu prototype_node clearfix"><center>{menu type="block" items=$menu_items}</center></div>

{/block}

{/container}