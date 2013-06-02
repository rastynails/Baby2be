{canvas blank}
    {container stylesheet="user_selector_popup.style"}

    <div class="user-selector-list stdmargin clearfix">
        {foreach from=$list item="item"}
            <div class="user-selector-item" data-email="{$item.address}" >
                <div class="user-selector-avatar"><img src="{$item.avatar.src}" title="{$item.avatar.title}" /></div>
                <div class="user-selector-content">
                    <div class="user-selector-item-title">{$item.title}</div>
                    <div class="user-selector-item-fields">{$item.fields}</div>
                </div>
                <div class="user-selector-check"></div>
            </div>
        {/foreach}
    </div>

    <div class="user-selector-send clearfix">
        {form UserSelectorPopup}
            <div class="user-selector-message-input">
                <div class="usm-selected">
                    <span class="us-selected-count">0</span> users selected
                </div>
               {* <div class="usm-label">
                    {label %message_label for="message"}
                </div> *}
                <div class="usm-input">
                    {input name="message"}
                </div>
            </div>

            <div class="usm-submit float_right">
                {button action="send"}
            </div>

        {/form}
    </div>

    {/container}
{/canvas}