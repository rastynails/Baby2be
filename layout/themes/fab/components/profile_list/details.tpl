{* Component Profile List *}

{canvas}

	{container stylesheet="profile_list.style" class="profile_list_contener"}
		<div class="clearfix">
			{if $tabs}
				{menu type='tabs-small' items=$tabs}
			{/if}
			<div class="clr_div"></div>
			{component SearchResultCount}
			{if $change_vm}
				<div class="change_vm_cont"><a href="{$view_mode.href}" class="{$view_mode.class}">{$view_mode.label}</a></div>
			{/if}
			{if $enable_pagging}
				{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages exclude="new_search"}
			{/if}
		</div>
		{counter start=1 skip=1 print=false assign='counter'}
	
		{foreach from=$list item=item}
			
		<div class="details_cont">

			{block}
			{block_cap title=$item.profile_label}{/block_cap}
			<div class="clearfix">
			<a name="id_{$item.profile_id}"></a>
				<div class="profile_list_thumb">
				    
				    <div style="position: relative; z-index: 98">   
                          {profile_thumb profile_id=$item.profile_id redirect_params=$item.url_params size=85}
                    </div>
				
				</div>
				<div class="ref_menu">
					{component ProfileReferences profile_id=$item.profile_id mode=$list_name}
				</div>
				<div class="profile_list_info">	
					<div class="pdet_info clearfix">
						<div class="membership_icon">{membership_icon profile_id=$item.profile_id}</div>			
				        <!--div class="pdet_name"></div-->
						<div class="pdet_sex">{$item.sex_label}, </div>
						    {foreach from=$item.age item=age}
								<div class="pdet_age">{$age},</div>
						    {/foreach}
	                    <div class="pdet_loc_value">
							{if $item.location.city}
							    {$item.location.city},
							{elseif !empty($item.location.custom_location)}
							     {$item.location.custom_location},
							{/if}

							{if $item.location.state}
							    {$item.location.state},
							{/if}

							{if $item.location.country}
							    {$item.location.country}.
							{/if}
						</div>
						<div class="clr_div"></div>
						{if $item.join_date}
							<span class="small">{text %.profile.list.join_date_label}: {$item.join_date}</span>
							
						{/if}
						{if $item.photo_count != null }
	                        <span title="{text %.profile.list.uploaded_photos_hint username=$item.username photo_count=$item.photo_count}" class="small">
	                            {if $item.photo_count}
	                                {text %.profile.list.photos_label}: <a href="{document_url doc_key='profile_photo' profile_id=$item.profile_id}">{$item.photo_count}</a>
	                            {else}
	                                {text %.profile.list.photos_label}: {$item.photo_count}
	                            {/if}
	                        </span>
						{/if}

	                    {if !empty($item.view_time) }
	                        <span class="small">
	                           {text %.profile.list.viewed_time}: {$item.view_time|spec_date}
	                        </span>
	                    {/if}
					</div>		
					{if $list_name == 'note_list'}
					       <div class="pl_note">
	                           {$item.general_description}
	                        </div>
					{else}
						<div class="pl_status">
						   <span>{text %status}:</span> {$item.textStatus}
						</div>
					{/if}
					{if $item.note}
					<div class='profile_note_value'>
					<span class="small">					
						{text %.components.profile_notes.note_about} {$item.profile_label}:<br />
						{$item.note}
					</span>
					</div>					
					{/if}	
					<div class="profile_activity">
					{if isset($item.activity_info.online)}
						{text %.profile.labels.activity}:
						<!--iminvite_href img=true username=$profile.username class='im_invite_txt' -->
						{online_btn profile_id=$item.profile_id}
					{elseif $item.activity_info.item}
						{text %.profile.labels.activity}:
						{$item.activity_info.item_num}&nbsp;{$item.activity_info.item_label}
					{/if}
					</div>
				</div>
			<div class="clr"></div>
			</div>
			{/block}
		</div>
		{counter}
			{if ($counter-1) == $ads_pos}
				{capture name=ads}{strip}
					{ads pos='profile_list'}
				{/strip}{/capture}
				{if $smarty.capture.ads}
					<br clear="all" />
					{block title=%.profile.list.ads_label}
						{$smarty.capture.ads}
					{/block}
				{/if}
			{/if}
		{/foreach}
	<div class="div_clr"></div>
		{if $enable_pagging}
			{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages exclude="new_search"}
		{/if}
	{/container}
	
{/canvas}
