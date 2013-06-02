{* component Cls Move Item *}

{container stylesheet="cls_move_item.style"}

<a href="javascript://" {id="move_item"}>{text %move_item}</a>

{*Move Item Thickbox*}
<div style="display: none">
	<div class="move_item_title"><h2>{text %move_item_title}</h2></div>
	<div class="move_item_content">
	{form ClsMoveItem}
		{input name="category"}
		{input name="item_id"}
		<div class="right">{button action="move"}</div>
	{/form}	
	</div>
</div>
{/container}