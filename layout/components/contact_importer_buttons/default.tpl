{container stylesheet="contact_importer_buttons.style" class="float_right contact_importer_buttons"}

{if $buttons.facebook}
<div class="fb_float_right">
    <button onclick="Fb_Invite();" class="fbib">{text %.components.invite_friends.fb_invite_txt}</button>
</div>
{/if}

{if $buttons.google}
<div class="gmail_float_right">
    <button class="gib" {id="google"}>{text %.components.invite_friends.fb_invite_txt}</button>
</div>
{/if}

{/container}