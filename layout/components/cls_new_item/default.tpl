{* component Cls New Item *}

{canvas}
{container class="centered_form"}

{if !$no_permission}
	{block title=%labels.new_item}
		{form ClsNewItem}
		<table class="form">
			<tr>
				<td class="label">{label for="item_type"}</td>
				<td class="value">{input name="item_type"}</td>
			</tr>		
			<tr>
				<td class="label">{label for="category"}</td>
				<td class="value">{input name="category"}</td>
			</tr>
			<tr>
				<td class="label">{label for="title"}</td>
				<td class="value">{input name="title"}</td>
			</tr>
			<tr>
				<td class="label">{label for="description"}</td>
				<td class="value">
					{input name="description"}
				 	{text %labels.description_desc} 	
				</td>
			</tr>	
			<tr>
				<td class="label">{label for="file"}</td>
				<td class="value file_input">
					{text %labels.file_desc}<br/> 
					{input name="file"}
				</td>
			</tr>	
			<tr>
				<td class="label">{label for="currency"}</td>
				<td class="value">{input name="currency"}</td>
			</tr>				
			<tr>
				<td class="label">{label for="price"}</td>
				<td class="value">{input name="price"}</td>
			</tr>
            {if $allow_payment}
			<tr>
                <td class="label">{label for="payment_dtls"}</td>
                <td class="value">{input name="payment_dtls"}</td>
			</tr>
			{/if}
			<tr>
				<td class="label">{label for="budget"}</td>
				<td class="value">
					{text %labels.budget_from} {input name="budget_from"}
					{text %labels.budget_to} {input name="budget_to"}
					{text %labels.budget_desc} 
				</td>
			</tr>
            {if $wanted_allow_comments || $offer_allow_comments}
			<tr>
				<td class="label">{label for="allow_comments"}</td>
				<td class="value">{input name="allow_comments"}</td>
			</tr>
            {/if}
            {if $wanted_allow_bids || $offer_allow_bids}
			<tr>
				<td class="label">{label for="allow_bids"}</td>
				<td class="value">{input name="allow_bids"}</td>
			</tr>
            {/if}
			<tr style="display: none;">
				<td class="label">{label for="wanted_limited_bids"}</td>
				<td class="value">{input name="wanted_limited_bids"}</td>
			</tr>	
			<tr style="display: none;">
				<td class="label">{label for="offer_limited_bids"}</td>
				<td class="value">{input name="offer_limited_bids"}</td>
			</tr>							
			<tr>
				<td class="label">{label for="start_date"}</td>
				<td class="value">
					{input name="start_date"}<br/>
					{input name="start_time"}	
					<div style="display: none;">{label for="start_time"}</div>
				</td>
			</tr>																													
			<tr>
				<td class="label">{label for="end_date"}</td>
				<td class="value">
					{input name="end_date"}<br/>
					{input name="end_time"}	
					<div style="display: none;">{label for="end_time"}</div>
				</td>
			</tr>			
		    <tr><td colspan="2" class="submit">{button action="post"}</td></tr>
		 </table>
		 {/form}
	{/block}
{else}
	<div class="no_content">{$no_permission}</div>
{/if}

{/container}
{/canvas}