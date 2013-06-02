{* MasterPage Neighbourhood List component *}
{canvas sidebar=yes}
	{include_style file="default.style"}
	
	{menu type='tabs-small' items=$neighbourhood_tabs}
	{if $mode == 'zip' || $mode == 'city'}
	<form method="GET">
		<input type="hidden" name="mode" value="{$mode}">
		<table class="neigh_table">
	    	<tr>
		    	<td>{text %.label.location_list_distance}<input type="text" value="{$distance}" name="distance">{text %.i18n.location.`$search_unit`}</td>
		        <td><input type="submit" value="OK"></td>
	    	</tr>
		</table>
	</form>
	{/if}
	{component NeighbourhoodList}

{/canvas}