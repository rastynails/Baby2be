{* component Cls Edit Item *}

{canvas}
{container class="centered_form report"}

{if $moderator}
{capture name=toolbar}
	{component ClsMoveItem item_id=$item_info.item_id group_id=$item_info.group_id entity=$item_info.entity}
{/capture}
{/if}

{block title=%edit_item_title toolbar=$smarty.capture.toolbar}

{form ClsEditItem}
<table class="form">
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
			{foreach from=$files item=file}
			<div {id=file_`$file.file_id` }style="margin: 2px; float: left; text-align: center;">
				<img height="100" src="{$file.file_url}"/><br/>
				<a href="javascript://" class="delete_item_file">[delete]</a>
			</div>
			{/foreach}
		</td>
	</tr>
	<tr>
		<td class="label">{label for="currency"}</td>
		<td class="value">{input name="currency"}</td>
	</tr>
	{if $item_info.entity == 'offer'}	
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
	{else}
	<tr>
		<td class="label">{label for="budget"}</td>
		<td class="value">
			{text %labels.budget_from} {input name="budget_from"}
			{text %labels.budget_to} {input name="budget_to"}
			{text %labels.budget_desc} 
		</td>
	</tr>
	{/if}
	{if $configs->allow_comments}	
	<tr>
		<td class="label">{label for="allow_comments"}</td>
		<td class="value">{input name="allow_comments"}</td>
	</tr>
	{if $configs->allow_bids}			
	<tr>
		<td class="label">{label for="allow_bids"}</td>
		<td class="value">{input name="allow_bids"}</td>
	</tr>	
	<tr>
		<td class="label">{label for="`$item_info.entity`_limited_bids"}</td>
		<td class="value">{input name="`$item_info.entity`_limited_bids"}</td>
	</tr>
	{/if}
	{/if}	
	<tr>
		<td class="label">{label for="start_date"}</td>
		<td class="value">
			{input name="start_date"}<br/>
			{input name="start_time"}	
		</td>
	</tr>																													
	<tr>
		<td class="label">{label for="end_date"}</td>
		<td class="value">
			{input name="end_date"}<br/>
			{input name="end_time"}	
		</td>
	</tr>			
    <tr>
    	<td colspan="2" class="submit">
    		{input name="item_id"}
    		{button action="edit"}
    	</td>
    </tr>
</table>
{/form}

{/block}

{/container}
{/canvas}