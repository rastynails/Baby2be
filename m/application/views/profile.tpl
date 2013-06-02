
{block}
<div class="brief_info_thumb">
	{profile_thumb profile_id=$profile_id size=70}
</div>
<div class="brief_info_cont">
	<span class="highlight">{$profile.username}</span>{if $profile.headline}: {$profile.headline}{/if}<br />
    {$profile.sex_label}{foreach from=$profile.age item=item}, {$item}{/foreach}
	{if $profile.location.city}, {$profile.location.city}{/if}
	{if $profile.location.state}, {$profile.location.state}{/if}
	{if $profile.location.country}, {$profile.location.country}.{/if}
	<br /><br />
	<div class="profile_activity">
		{if isset($profile.activity_info.online)}
			{text section='profile.labels' key='activity'}:
			{online_btn profile_id=$profile_id display='link'}
		{elseif $profile.activity_info.item}
			{text section='profile.labels' key='activity'}:
			{$profile.activity_info.item_num}&nbsp;{$profile.activity_info.item_label}
		{/if}
	</div>
</div>
<div style="clear:both;"></div>
{/block}

{if !$is_owner}
{block}
	<a class="btn" href="{$url_sendmsg}">{text section='components.send_message' key='send_label'}</a>
	{if $allow_bookmarking}
	{if $bm_ref == 'bookmark'}
		<a class="btn" href="{$base_url}profile/bookmark/{$profile.username}">{text section='components.profile_references.labels' key='bookmark'}</a>
	{else}
		<a class="btn" href="{$base_url}profile/unbookmark/{$profile.username}">{text section='profile.list' key='unbookmark_label'}</a>
	{/if}
	{/if}
	{if isset($profile.activity_info.online)}{online_btn profile_id=$profile_id display='btn' label='IM'}{/if}
{/block}
{/if}

{block_cap}{$fields_sect}{/block_cap}
{block}
	{foreach from=$fields key="page_id" item="page" name='p'}
	{if $smarty.foreach.p.iteration == 1}
		<table class="form small">
			{foreach from=$page key="section_id" item="section"}
				<tr>
					<th colspan="2">
						{text section=profile_fields.section key=$section_id}
					</th>
				</tr>
				{foreach from=$section key="field_id" item="field"}
				<tr>
				<td class="label" style="width: 25%">{text section='profile_fields.label_view' key=$field_id}</td>
				{if $field.type=="text"}
					<td class="value">
						{if $field.presentation=="url"}
							<a href="{$field.value}">{$field.value|wordwrap:60:"<br>\n":true}</a>
						{elseif $field.presentation=="callto"}
							<a href="callto:{$field.value}">{$field.value|wordwrap:60:"<br>\n":true}</a>
						{else}
							{$field.value}
						{/if}
					</td>

				{elseif $field.type=="array"}
					<td class="value">
						{strip}
							{foreach from=$field.value item=item name="f"}
								{if $field.match}
									{text section='profile_fields.value' key=`$field.match`_`$item`}
								{else}
									{text section='profile_fields.value' key=`$field.name`_`$item`}
								{/if}
								{if !$smarty.foreach.f.last}, {/if}
							{/foreach}
						{/strip}
					</td>
				{/if}
				</tr>
			{/foreach}
		{/foreach}
		</table>
	{/if}
	{/foreach}
{/block}

{if $photos}
	{block_cap}{$photo_sect}{/block_cap}
	{block}
		{foreach from=$photos item='photo'}
			<a href="{$photo_url}{$photo.photo_id}"><img src='{$photo.thumb_url}' width='60' /></a>
		{/foreach}
		<div class="small"><a href="{$allphotos_url}">{text section='mobile' key='view_more'}</a></div>
	{/block}
{/if}