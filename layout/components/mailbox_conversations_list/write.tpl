{* component MailboxConversationsList *}
{canvas}

{container stylesheet="mailbox_conversations_list.style"}
    {block_cap title=%submenu_item_write}
        {menu type="block" items=$mailbox_menu_items}
    {/block_cap}
    
    {block}
        {form SendMessagePage}
        <table class="form">
            <tr>
                <td class="label">{label %.forms.send_message_page.fields.recipient for='recipient'}:</td>
                <td class="value">{input name="recipient"}</td>
            </tr>
            <tr>
                <td class="label">{label %.forms.send_message.fields.subject for='subject'}:</td>
                <td class="value all_row_width">{input name="subject"}</td>
            </tr>
            <tr>
                <td class="label">{label %.forms.send_message.fields.text for='text'}:</td>
                <td class="value">{text_formatter for='text' entity="mailbox" controls="bold,italic,underline,link,emoticon,image"}{input name="text"}</td>
            </tr>
        </table>
        <br />
        <p class="center">{button action='send'}</p>
        {/form}
    {/block}
{/container}

{/canvas}
