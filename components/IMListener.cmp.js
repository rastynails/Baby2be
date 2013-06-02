
function component_IMListener(auto_id)
{
    this.DOMConstruct('IMListener', auto_id);

    // global reference
    if (!window.SK_IMListener) {
        window.SK_IMListener = this;
    } else {
        throw new SK_Exception('window.SK_IMListener already created');
    }

    this.$enable_sound = false;
    this.swf_player_src = '';
    var handler = this;

    this.delegates = {
        invClick: function(event) {
            var self = this;
            SK_openIM(event.data.sender_id).onIMOpen = function(){
                $(self).remove();
                delete handler.drawn_invitations[event.data.im_session_id];
                handler.drawn_invitations_count--;
                if (handler.drawn_invitations_count == 0)
                {
                    $(handler.container_node).css('display', 'none');
                }
            };
        }
    }
}

component_IMListener.prototype =
    new SK_ComponentHandler({

        /**
	    * Custom constructor.
	    */
        construct: function(ping_interval, enable_sound) {
            this.drawn_invitations = {}; // indexed by im_session_id
            this.drawn_invitations_count = 0;
            this.$enable_sound = enable_sound;

            var handler = this;

            SK_Ping.getInstance().addCommand('imListener', {
                params: {
                    drawn_invitations: handler.drawn_invitations
                },
                before: function()
                {
                    this.params.drawn_invitations = handler.drawn_invitations;
                },
                after: function( res )
                {
                    if ( res.js )
                    {
                        (new Function(res.js)).call(handler);
                    }
                }
            }).start(ping_interval);
        },

        drawInvitations: function(inv_list)
        {
            if (this.$('#im_invitations').css('display', 'none'))
            {
                this.$('#im_invitations').css('display', 'block');
            }

            for ( var i = 0, inv, $inv; inv = inv_list[i]; i++ )
            {
                if (this.drawn_invitations[inv.im_session_id]) {
                    continue;
                }

                $inv = this.$('#im_invitaion_tpl')
                .clone().removeAttr('id');
                
                this.$('#im_invitations').find('.block_cap').after($inv);

                $inv.find('.block_body_c').html(inv.message);
                if (this.$enable_sound)
                {
                    var im_player = new SWFObject( this.swf_player_src, "im_listener_sound_player_embed", 100, 25, "9" );
                    im_player.addParam("allowfullscreen","false");
                    im_player.addVariable("file", SITE_URL+"static/sound/receive.mp3");
                    im_player.addVariable("autostart", 'yes');

                    im_player.write("im_sound_player");
                }

                $inv.bind('click', {
                    sender_id: inv.sender_id,
                    im_session_id: inv.im_session_id
                }, this.delegates.invClick);

                this.drawn_invitations[inv.im_session_id] = true;
                this.drawn_invitations_count++;
            }
        }

    });
