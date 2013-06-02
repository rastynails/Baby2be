{* Event httpdoc template *}

{canvas}
<div style="float:left; width:70%;">
    {block title=%.event.cap_label_event_speed_dating_edit}
		{form EventSpeedDatingEdit}
        	<table class="form">
                <tbody>
                    <tr>
                        <td class="label">{label %.event.field_title for='event_title'}</td>
                        <td class="value">{input name='event_title'}</td>
                    </tr>
                    <tr>
                    	<td class="label">{label %.event.field_date for='date'}</td>
                        <td class="value">{input name='date'}</td>
                    </tr>
                    <tr>
                    	<td class="label">{label %.event.field_starts_at for='start_time'}</td>
                        <td class="value">{input name='start_time'}</td>
                    </tr>
                    <tr>
                    	<td class="label">{label %.event.field_ends_at for='end_time'}</td>
                        <td class="value">{input name='end_time'}</td>
                    </tr>
                    <tr>
                    	<td class="label">{label %.event.field_image for='file_speed_dating'}</td>
                        <td class="value">
                        	{if $img_url}<div {id="img_file_cont"}><img src="{$img_url}" width="100" /> <a href="javascript://">Delete</a></div>{/if}
                        	<div {id="input_file_cont"}{if $img_url} style="display:none;"{/if}>{input name='file_speed_dating'}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">{label %.event.field_desc for='event_desc'}</td>
                        <td class="value">{input name='event_desc'}</td>
                    </tr>
                    <tr>
                        <td class="label">{label %.event.field_search_by_location for='search_by_location'}</td>
                        <td class="value">{input name='search_by_location'}</td>
                    </tr>        
                     <tr>
                        <td class="label">{label %.event.field_i_am_attanding for='i_am_attending'}</td>
                        <td class="value">{input name='i_am_attending'}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:right;">{button action='event_speed_dating_edit'}</td>
                    </tr>
                </tbody>
            </table>
        {/form}
    {/block}
</div>
<div style="float:right; width:29%">{component $tags_cmp}</div>
<br clear="all" />
{/canvas}
