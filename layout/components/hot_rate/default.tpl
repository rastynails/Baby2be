{* Component Hot Rate *}

{canvas}
	{container stylesheet="hot_rate.style"}
		{block class="center_block"}
			{block_cap title=%title}
				<div class="sex_switch float_right">
					<select>
						<option value="">{text %default_sex}</option>
						{foreach from=$sexes item=item}
							<option value="{$item.id}">{$item.label}</option>
						{/foreach}
					</select>
				</div>	
			{/block_cap}
				{if $photo}
					<div class="center_block rate_container">
						<div class="rate">
							{component $rate}
						</div>
						<div class="skip">
							<a href="javascript://"><b>{text %skip_label}</b></a>
						</div>
						<div class="clr"></div>
					</div>
					<div class="photo_container center" style="background: url({$photo.url}) center no-repeat; height: {$height}px">
					</div>
				{else}
					<div class="photo_container center">
						<div class="no_content">{text %no_photo}</div>
					</div>	
				{/if}
			
			<br />
			<div class="profile_link" {if !$photo}style="display: none"{/if}>
				<a href="{document_url doc_key='profile' profile_id=$photo.profile_id}">
				{text %profile_label}
				</a>
			</div>
		{/block}
		<br />

	{/container}
{/canvas}