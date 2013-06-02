{* component Restore Password *}

{canvas}
{container}

    <div class="center_block">
    {block title=%title}
        {form RestorePassword}
        <table class="form">
            <tr>
                <td class="label">{label %.forms.change_password.fields.new_password.label for="new_password"}</td><td class="value">{input name="new_password"}</td>
            </tr>
            <tr>
                <td class="label">{label %.forms.change_password.fields.new_password_confirm.label for="new_password_confirm"}</td><td class="value">{input name="new_password_confirm"}</td>
            </tr>
            <tr>
                <td align="center" colspan="2">{button action="change" label=%.forms.change_password.actions.change}</td>
            </tr>
        </table>
        {/form}
    {/block}
    </div>
    
{/container}
{/canvas}
