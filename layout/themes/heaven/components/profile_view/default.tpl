{canvas}
	{container stylesheet="style.style"}

	<div class="dd_area" {id="dd_area"}>
     
     {block class="phv_block"}
     {block_cap}{/block_cap}
        <div class="pv_header clearfix">
            <div class="float_left">
                {component ProfileStatusView profile_id=$actorId}
            </div>
            <div class="float_right">
				<div class="search_btns_wrap"><div class="search_btns">{component ProfileCarousel}</div></div>          	
				<div class="clr_div"></div>
				<div class="report_btn">{component $report}</div>
			</div>
        </div>
		<div class="member_actions clearfix">{component ProfileActions profile_id=$actorId}</div>       
        {if $owner_mode}
            {component $profile_component_select}
        {/if}
     {/block}
	<div style="padding:3px 0;">{component $profile_background}</div>
	{if $is_private}<div style="padding:20px;text-align:center;">{text %private_profile_message}</div>{/if}
	<div class="float_half_left narrow pv_left">
		<div class="ddbox">{component $profile_notes}</div>
	<div {id="photo_album"} class="ddbox">{if $photo_album_cmp}{component $photo_album_cmp}{/if}</div>
	{foreach from=$left_cmp item='cmp'}
		<div {id="vcmp_`$cmp.id`"} class="ddbox">
			{component $cmp.cmp}
		</div>
	{/foreach}
	</div>
	<div class="float_half_right wide pv_right">
		<div {id="profile_dtls"} class="ddbox">{if $profile_dtls}{component $profile_dtls}{/if}</div>
		{foreach from=$right_cmp item='cmp'}
			<div {id="vcmp_`$cmp.id`"} class="ddbox">
				{component $cmp.cmp}
			</div>
		{/foreach}
		{if $comments_cmp}{component $comments_cmp}{/if}
	</div>
	<div class="clr_div"></div>

	</div>
	{/container}
{/canvas}
