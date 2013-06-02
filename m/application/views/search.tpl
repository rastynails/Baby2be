
{block}

{if !empty($service_msg)}
    <div class="not_found">{$service_msg}</div>
{else}
<form method="get" action="{$form_action}">
	<b><label>{text section='profile_fields.label_search' key=$fields.sex.id}</label></b>
	<ul id="sex">
		{foreach from=$fields.sex.values item='val'}
		<li class="{cycle values='even,odd'}">
			<input name="sex" id="sex_{$val}" type="radio" value="{$val}" /><label for="sex_{$val}">{text section='profile_fields.value' key=`$fields.sex.prefix`_`$val`}</label>
		</li>
		{/foreach}
	</ul><br clear="all" />
	
	<b><label>{text section='profile_fields.label_search' key=$fields.match_sex.id}</label></b>
	<ul id="match_sex">
		{foreach from=$fields.match_sex.values item='val'}
		<li class="{cycle values='even,odd'}">
			<input name="match_sex[]" id="match_sex_{$val}" type="checkbox" value="{$val}" /><label for="match_sex_{$val}">{text section='profile_fields.value' key=`$fields.match_sex.prefix`_`$val`}</label>
		</li>
		{/foreach}
	</ul>
	<br clear="all" />
	
	<b><label>{text section='profile_fields.label_search' key=$fields.birthdate.id}</label></b>
	<div>
		{text section='forms._fields.age_range' key='from'}
		<select name="birthdate[0]">
			{foreach from=$fields.birthdate.from item='opt'}
			<option value="{$opt}">{$opt}</option>
			{/foreach}
		</select>
		{text section='forms._fields.age_range' key='to'}
		<select name="birthdate[1]">
			{foreach from=$fields.birthdate.to item='opt'}
			<option value="{$opt}">{$opt}</option>
			{/foreach}
		</select>
		{text section='forms._fields.age_range' key='years_old'}
	</div>
	<!-- <div class="center"><input name=location /></div> --><br />
	
	<b><label>Location</label></b><br />
	
	<input type="text" value="" name="location[radius]" size="4"/>{$units}
	<input type="text" value="" name="location[zip]" size="9"/>{text section='forms._fields.mileage' key='zip'}
	<br /><br />
	
	<div><label><input id="search_online_only" name="search_online_only" type="checkbox" value="1" />{text section='profile_fields.label_search' key=$fields.search_online_only.id}</label></div>
	<div><label><input id="search_with_photo_only" name="search_with_photo_only" type="checkbox" value="1" />{text section='profile_fields.label_search' key=$fields.search_with_photo_only.id}</label></div>
					
	<div class="submit"><input type="submit" value="Search" /></div>
</form>
{/if}
{/block}