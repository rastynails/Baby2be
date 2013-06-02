{* component IM *}
{canvas blank}

{container stylesheet="im.style"}
{if $im_is_available}
    <div {id="ads_top"}>
    {ads pos='top'}
    </div>
    <div style='background-color: white; height: 20px; text-align: right;'>
    {if $session_length}
    <div {id="esd_countdown_container"}>
    <input type='hidden' id="esd_countdown" value='{$session_length}' />
    <label>Elapsed time:</label>
    <label id="esd_countdown_label" style='margin-right: 5px;'></label>
    </div>
    {/if}
    {if $opponent_service_use_limited}<div class="center"><span class="msg_text"><b>{text %msg_opponent_service_use_limited}</b></span></div>{/if}
    </div>


    <ul style="display: none">
        {* message template node. js logic sets classes: "my_msg" or "opp_msg" *}
        <li {id="message_tpl"}>
            <span class="msg_time small"></span>&nbsp;
            <a href="#" class="msg_username"></a><br />
            <span class="msg_text"></span>
        </li>
    </ul>

    <div {id="window"} class="im_window">
        <ul {id="msg_list"}></ul>
    </div>

    <div {id="sidebar"} class="im_sidebar">
        {profile_thumb profile_id=$opponent->profile_id}<br/>
        <a href="#" class="opp_username sex_ico-{$opponent->sex}">{$opponent->username}</a>
    </div>

    <div {id="bottom"} class="im_bottom">
    <form {id="input_form"} onsubmit="return false">
        <div class="im_bottom_inventory">
            {palette_picker id="msg_color"}
            {smileset for="input"}
            {if $im_enable_sound}
            <span class="text_formatter">
                <a href="javascript://" title="{text %enable_sound}" class="chat_sound chat_control" ></a>
            </span>
            {/if}
        </div>
        <div style="clear: both;"></div>
        <div class="im_input_container">
            <input type="text" {id="input"} name="text_entry" class="im_input" />
            <input type="submit" {id="send_btn"} class="im_send_btn" value="{text %send_button}" />
        </div>
    </form>
    <br clear="all" />
    <div {id="ads_bottom"}>
    {ads pos='bottom'}
    </div>
    </div>
    {if $im_enable_sound}
        <div id="im_sound_player" style="position: absolute; top: -1000px;"></div>
    {/if}
{else}
    <span>{$permission_message}</span>
{/if}
{/container}
{/canvas}
