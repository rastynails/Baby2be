{* component Shoutbox *}

{container stylesheet="shoutbox.style"}
{block title=$label_shoutbox}
{if $read_permission_message}
<div>
    {$read_permission_message}
</div>
{else}
<ul style="display: none">
    {* message template node. js logic sets classes: "my_msg" or "opp_msg" *}
    <li {id="message_tpl"}  class="clearfix">
        <div class="shoutbox_avatar">
        <a href="#" class="profile_thumb_wrapper">
            <img width="45" class="profile_thumb" src="#">
        </a>
        </div>
        <div class="shoutbox_body">
        	<p class="msg_block"><a href="#" class="msg_username"></a>:&nbsp;<span class="msg_text"></span></p>
	        <span class="msg_time small"></span>
            {if $isModerator}
                <div class="shoutbox_delete"><a href="javascript://"></a></div>
            {/if}
        </div>
	</li>
</ul>

<div {id="window"} class="shoutbox_window">
    <ul {id="msg_list"} >{if $no_messages}<li><div class="center" id="no_messages_label">{text %no_messages}</div></li>{else}<li><div class='shoutbox_preloader'></div></li>{/if}</ul>
</div>
{if $write_permission_message}
<div>
    {$write_permission_message}
</div>
{else}
<div {id="bottom"} class="shoutbox_bottom">
    <form {id="shoutbox_input_form"} onsubmit="return false">
        <div class="shoutbox_input_container" >
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="top">
                        <div class="clearfix">
                            <div class="shoutbox_right">
                                {if $is_guest}                                
                                    <input type="text" {id="username"} name="username" class="shoutbox_input" size="14" />
                                    {$username_invitation}
                                {/if}
                            </div>
                            <div class="clr_div"></div>
                            <div class="shoutbox_right">                                
                                <input type="text" {id="input"} name="text_entry" class="shoutbox_input" size="14"/>
                               {$text_entry_invitation}
                            </div>
                        </div>
                    </td>
                    <td valign="bottom" nowrap="nowrap">
                        <div class="shoutbox_left">{palette_picker id="msg_color"}</div>
                        <div class="shoutbox_left">{smileset for="input"}</div>
                        <input type="submit" {id="send_btn"} class="shoutbox_send_btn" value="{text %send_button}" />
                               <div class="clr_div"></div>
                    </td>
                </tr>
            </table>
        </div>
    </form>
</div>
{/if}
{/if}
{/block}
{/container}
