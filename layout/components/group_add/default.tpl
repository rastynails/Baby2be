{canvas}
    {container}
        <div class="center_block wide">
            {block title=%.forms.group_add.form_title}
                {if isset($error_msg)}
                        <div class="no_content">{$error_msg}</div>
                {else}
                    {form GroupAdd}
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
                                    <td class="value">{input name='photo'}</td>
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
                                    <td colspan="2" class="block_submit right">
                                        {button action='process'}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    {/form}
                {/if}
            {/block}
        </div>
    {/container}
{/canvas}