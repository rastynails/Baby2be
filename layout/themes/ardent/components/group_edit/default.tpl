
{canvas}
{container stylesheet="group_edit.style"}
	{if isset($error)}
		<div class="no_content">{$error}</div>	
	{else}
		<div class="right smallmargin"><input type="button" value="{text %del_btn}" {id="delete_btn"} /></div>
        <div class="clearfix"></div>
		<div class="float_half_left wide">
		{block title=%form_title}
			{form GroupEdit}
	        	<table class="form">
	                <tbody>
	                    <tr>
	                        <td class="label">{label for='title'}</td>
	                        <td class="value">{input name='title'}</td>
	                    </tr>
	                    <tr>
	                        <td class="label">{label for='description'}</td>
	                        <td class="value">{input name='description'}</td>
	                    </tr>
	                    <tr>
	                    	<td class="label">{label for='photo'}</td>
	                        <td class="value">
	                        	{if $group_image}<div {id="img_file_cont"}><img src="{$group_image}" width="330" /><br /><a href="javascript://">{text %.forms._fields.file.delete_btn}</a></div>{/if}
	                        	<div {id="input_file_cont"}{if $group_image} style="display: none;"{/if}>{input name='photo'}</div>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td class="label">{label for='browse_type'}</td>
	                        <td class="value">{input name='browse_type' labelsection='forms.group_add.fields.browse_type'}</td>
	                    </tr>                  
	                    <tr>
	                    	<td class="label">{label for='join_type'}</td>
	                        <td class="value">
		                        {capture name="cb_label"}
	                        		{text %.forms.group_add.fields.allow_claim.label}
	                        	{/capture}
	                        	{input name='join_type' labelsection='forms.group_add.fields.join_type' label=$smarty.capture.cb_label}
	                        </td>
	                    </tr>
	                    <tr>
	                        <td colspan="2" class="block_submit">{button action='process'}</td>
	                    </tr>
	                </tbody>
	            </table>
	        {/form}
		{/block}
		</div>
		
		<div class="float_half_right narrow">
			{component $moderators_cmp}
		</div>
	{/if}
{/container}
{/canvas}