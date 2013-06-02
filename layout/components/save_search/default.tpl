{* component Save Search *}

{container class="save_search"}

	<a href="javascript://" {id="btn"}>{text %label}</a>
	<div {id="thick_title"} style="display:none"><b>{text %label}</b></div>
	<div {id="thick_content"} style="display:none">
		<div class="save_search_floatbox">
		{form SaveSearch}
			{input name="name"}&nbsp;{button action="save"}
		{/form}
		</div>
	</div>

{/container}