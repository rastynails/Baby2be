{container stylesheet="album_control.style" class="album_control"}
	<div style="display: none">
		<div {id='fb_content'}>
			{text %delete_confirm}
		</div>
	</div>

	<div class="album_details_cont">
	{capture name=toolbar}
	    <div class="controls">
	       <a href="javascript://" class="edit">{text %edit}</a> | <a href="javascript://" class="delete">{text %remove}</a>
	    </div>
    {/capture}

	{block title=%title toolbar=$smarty.capture.toolbar}
	   <table class="form">
			<tbody>
			    <tr>
			        <td class="label">
			            {text %name_label}
			        </td>
			        <td class="value">
			            {$album->getView_label()}
			        </td>
			    </tr>
			    <tr>
                    <td class="label">
                        {text %privacy_label}
                    </td>
                    <td class="value">
                        {assign var=privacy value=$album->getPrivacy()}
                        <span class="privacy_{$privacy}">{text %privacy_values.`$privacy`}</span>
                    </td>
                </tr>
			</tbody>
        </table>
	{/block}
	</div>

	<div class="album_edit_cont" style="display: none">

    {capture name=toolbar}
        <div class="controls">
           <a href="javascript://" class="delete">{text %remove}</a>
        </div>
    {/capture}

    {block title=%title toolbar=$smarty.capture.toolbar}
    {form EditAlbum}
       <table class="form">
            <tbody>
                <tr>
                    <td class="label">
                        {label %name_label for=label}
                    </td>
                    <td class="value">
                        {input name=label}
                    </td>
                </tr>
                <tr>
                    <td class="label">
                        {label %privacy_label for=privacy}
                    </td>
                    <td class="value">
                        {input name=privacy labelsection=%privacy_values class="album_privacy"}
                    </td>
                </tr>
                <tr class="password_c" style="display: none">
                    <td class="label">
                        {label %password_label for=password}
                    </td>
                    <td class="value">
                        {input name=password}
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" align="center" style="padding-top: 5px;">
                        {button action=save} <input type="button" class="cancel_edditing" value="{text %cancel}" />
                    </td>
                </tr>
            </tfoot>
        </table>
    {/form}
    {/block}
    </div>



{*
	<div class="controls">
		<input type="button" class="edit" value="{text %edit}">
		<input type="button" class="save" value="{text %save}" style="display: none">
		<input type="button" class="delete" value="{text %remove}">
	</div>
	<div class="edit_cont">
		<input type="text" class="label" value="{$album->getLabel()}">
	</div>
	<br clear="all" />
*}
	{*<div style="display: none">
		{form EditAlbum}
			<table class="form">
				<tbody>
					<tr>
						<td class="label">
							{label for=label}
						</td>
						<td class="value">
							{input name=label}
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2" align="right">
							{button action="save"}
						</td>
					</tr>
				</tfoot>
			</table>
		{/form}
	</div>*}
{/container}