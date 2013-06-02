{* Component Profile List *}

{canvas}

	{container stylesheet="profile_list.style" class="profile_list_contener"}
		{if $tabs}
			{menu type='tabs-small' items=$tabs}
		{/if}
		{component SearchResultCount}
		<div class="clearfix">
			{if $change_vm}
				<div class="change_vm_cont"><a href="{$view_mode.href}" class="{$view_mode.class}">{$view_mode.label}</a></div>
			{/if}
			{if $enable_pagging}
				{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages exclude="new_search"}
			{/if}
			<div class="clr_div"></div>
			{counter start=1 skip=1 print=false assign='counter'}
			{foreach from=$list item=item}
				
			<div class="details_cont">
				<a name="id_{$item.profile_id}"></a>
				{block}
					{block_cap title=$item.profile_label}{/block_cap}
					<div class="profile_list_thumb">
					    
				  <!-- div style="position: relative; z-index: 98" -->   
				     {profile_thumb profile_id=$item.profile_id redirect_params=$item.url_params}
				   <!-- /div -->
					
						<div class="profile_activity">
						{if isset($item.activity_info.online)}
							{text %.profile.labels.activity}:<br>
							<!--iminvite_href img=true username=$profile.username class='im_invite_txt' -->
							{online_btn profile_id=$item.profile_id}
						{elseif $item.activity_info.item}
							{text %.profile.labels.activity}:<br>
							{$item.activity_info.item_num}&nbsp;{$item.activity_info.item_label}
						{/if}
						</div>
					</div>
					<div class="ref_menu">
						{component ProfileReferences profile_id=$item.profile_id mode=$list_name}
					</div>
					<div class="profile_list_info">
						<div class="membership_icon">{membership_icon profile_id=$item.profile_id}</div>				
						{$item.sex_label}, 
						{foreach from=$item.age item=age}
							{$age},
						{/foreach}
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
						<br />
						{if $item.join_date}
							<span class="small">{text %.profile.list.join_date_label}: {$item.join_date}</span>
							<br />
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
						<br />
                    {if !empty($item.view_time) }
                        <span class="small">
                           {text %.profile.list.viewed_time}: {$item.view_time|spec_date}
                        </span>
                    {/if}
						<br /><br />
						<span class="small">
							{$item.general_description|wordwrap:62:"<br />":true|strip_tags|nl2br}
						</span>
						{if $item.note}
						<br /><br />
						<span class="small">					
							{text %.components.profile_notes.note_about} {$item.profile_label}:<br />
							{$item.note}
						</span>
						{/if}						
					</div>
				<div class="clr_div"></div>
				{/block}
			</div>
			{counter}
				{if ($counter-1) == $ads_pos}
					{capture name=ads}{strip}
						{ads pos='profile_list'}
					{/strip}{/capture}
					{if $smarty.capture.ads}
						<div class="clr_div"></div>
						{block title=%.profile.list.ads_label}
							{$smarty.capture.ads}
						{/block}
					{/if}
				{/if}
			{/foreach}
			<br clear="all" />
			{if $enable_pagging}
				{paging total=$paging.total on_page=$paging.on_page pages=$paging.pages exclude="new_search"}
			{/if}
		</div>
	{/container}
	
{/canvas}
