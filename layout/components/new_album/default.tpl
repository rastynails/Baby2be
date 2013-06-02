{container stylesheet="new_album.style"}
	{block title=%title}
		{if $exceeded}
			<div class="block_info">
				{text %exceeded}
			</div>
		{else}
			{form NewAlbum}
				<table class="form">
					<tbody>
						<tr>
							<td class="label">
								{label %.components.album_control.name_label for=label}
							</td>
							<td class="value">
								{input name=label}
							</td>
						</tr>
						<tr>
                            <td class="label">
                                {label %.components.album_control.privacy_label for=privacy}
                            </td>
                            <td class="value">
                                {input name=privacy labelsection=%.components.album_control.privacy_values class="album_privacy"}
                            </td>
                        </tr>
                        
                        <tr class="password_c" style="display: none">
                            <td class="label">
                                {label %.components.album_control.password_label for=privacy}
                            </td>
                            <td class="value">
                                {input name=password}
                            </td>
                        </tr>
                        
					</tbody>
					<tfoot>
						<tr>
							<td colspan="2" align="right">
								{button action="create"}
							</td>
						</tr>
					</tfoot>
				</table>
			{/form}
		{/if}
	{/block}
	
{literal}	
<script>
$('.album_privacy').change(function(){
    if ($(this).val() == 'password_protected')
    {
        $('.password_c').show();
    }
    else
    {
    	$('.password_c').hide();
    }
});
</script>
{/literal}

{/container}