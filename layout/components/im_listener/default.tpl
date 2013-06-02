{* component IM Listener *}

{container stylesheet="im_listener.style"}

<div style="display: none;">
	<div {id="im_invitaion_tpl"} class="block_body">
        <div class="block_body_r">
            <div class="block_body_c clearfix"></div>
        </div>
	</div>
</div>

{if $im_enable_sound}
    <div id="im_sound_player" style="position: absolute; top: -1000px;"></div>
{/if}

<div {id="im_invitations"} class="block im_invitation" style="display: none;">
    <div class="block_cap">
        <div class="block_cap_r">
            <div class="block_cap_c clearfix">
                <h3 class="block_cap_title">{text %.components.im_listener.new_invitation_title}</h3>
            </div>
        </div>
    </div>
    <div class="block_bottom">
        <div class="block_bottom_r"><div class="block_bottom_c"></div></div>
    </div>
</div>

{/container}
