{* Component Profile List *}

{container stylesheet="index_profile_list.style" class="index_profile_list"}

{block}
{block_cap title=%title}
	<div class="menu prototype_node">{menu type="block" items=$menu_items}</div>
{/block_cap}
<div class="profile_list_cont">
	<ul class="list prototype_node">
		<li class="item">
		    <a href="javascript://" class="label"></a>
			<a href="javascript://" class="image profile_thumb_wrapper"></a>
			<div class="sex_line"></div>
			
		</li>
	</ul>
	<div class="clr"></div>
</div>

<div class="empty_list_msg no_content" style="display:none">{text %no_items}</div>
<div class="index_more"><a href="javascript://" {id="view_more_btn"}>{text %view_more}</a></div>
<div class="clr_div"></div>
{/block}

{/container}