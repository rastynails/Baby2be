
function component_IM(auto_id)
{
	this.DOMConstruct('IM', auto_id);

	this.$window = this.$('#window');
	this.$msg_list = this.$('#msg_list');
	this.$sidebar = this.$('#sidebar');
	this.$bottom = this.$('#bottom');
	this.$input = this.$('#input');
	this.$send_btn = this.$('#send_btn');
	this.$enable_sound = false;
    this.is_esd_session = false;
    this.swf_player_src = '';
    this.$ads_top = this.$('#ads_top');
    this.$ads_bottom = this.$('#ads_bottom');
	var handler = this;


	var fitWindow = function() {
		var win_width = jQuery(window).innerWidth();
		var win_height = jQuery(window).innerHeight();

		var im_win_width = win_width - handler.$sidebar.outerWidth();
        var $adsBottomHeight = 0;
        if (typeof handler.$ads_bottom != 'undefined')
        {
            $adsBottomHeight = handler.$ads_bottom.outerHeight();
        }

		var im_win_height = win_height - handler.$bottom.outerHeight() - $adsBottomHeight - 20;
		handler.$window.width(im_win_width - 4); // 4 - for paddings
		handler.$window.height(im_win_height - 4); // 4 - for paddings

        var $adsTopHeight = 0;
        if (typeof handler.$ads_top != 'undefined')
        {
            $adsTopHeight = handler.$ads_top.outerHeight();
        }
		handler.$sidebar.css('margin-top', $adsTopHeight + 20);

		var input_width = im_win_width - handler.$send_btn.outerWidth();
		handler.$input.width(input_width - 20); // button paddings
	}

	if (!jQuery.browser.mozilla) {
		fitWindow();
	}
	else {
		// otherwise FF failed on $sidebar.width()
		window.setTimeout(function() {
			fitWindow();
		}, 100);

		window.setTimeout(function() {
			fitWindow();
		}, 500);
	}

	jQuery(window)
		.resize(function() {
			fitWindow();
		});


	this.msg_entries = [];

	this.$('#input_form')
		.submit(function() {
			var value = jQuery.trim(handler.$input.val());
			if (value) {
				var color_txt = handler.$('#message_tpl .msg_text').css('color');
				var color = handler.palette_picker.color || handler.palette_picker.rgbToHex(color_txt);

				var msg = {
						text: value,
						color: color
				};
				handler.msg_entries.push(msg);
			}

			handler.$input.val('').focus();

			return false;
		});


	this.delegates =
	{
		ping: function() {
			handler.ping();
		},

		pingComplete: function() {
			handler.ping_in_process = false;
			if (handler.ping_queued) {
				handler.ping();
			}
		},

		countdown: function(countdown_id,  countdown_value ) {
			countdown_value--;
			if ( countdown_value <= 0 )
			{
				handler.$('#esd_countdown_container').css('display', 'none');
				window.clearInterval( handler.esd_countdown );
			}
			$( countdown_id ).attr( 'value', countdown_value );
            sec = countdown_value % 60;
            if (sec < 10)
                sec = '0'+sec;
            countdown_value = Math.floor(countdown_value / 60) + ':' + sec;
			$("#esd_countdown_label").html( countdown_value );
		}

	}

	if (typeof window.onIMOpen != "undefined" && window.onIMOpen !== null )
	{
		window.onIMOpen.apply(this);
	}
}

component_IM.prototype =
	new SK_ComponentHandler({

	/**
	 * Custom constructor.
	 */
	construct: function(opponent_id, opponent_href, ping_interval, is_esd_session, enable_sound)
	{
		handler = this;
		this.opponent_id = opponent_id;
		this.last_message_id = null;
        this.$enable_sound = enable_sound;
        this.is_esd_session = is_esd_session;

		this.esd_countdown = window.setInterval( function() {handler.delegates.countdown('#esd_countdown', $( "#esd_countdown" ).attr('value') );}, 1000);
		this.ping_interval = window.setInterval(this.delegates.ping, ping_interval);
		this.ping(); // calling first ping faster

		this.$sidebar.find('a')
			.click(function() {
				window.opener.open(opponent_href);
				return false;
			});

       this.$(".chat_sound").bind("click", function() {
           if (handler.$enable_sound )
               {
                    handler.$enable_sound = false;
                    $(".chat_sound").addClass('no_chat_sound');
               }
               else
               {
                   handler.$enable_sound = true;
                   $(".chat_sound").removeClass('no_chat_sound');
               }
		});
	},


	/**
	 * Backend ping.
	 */
	ping: function()
	{
		if (this.ping_in_process) {
			this.ping_queued = true;
			return;
		}

		this.ping_queued = false;
		this.ping_in_process = true;

		var params = {
			opponent_id: this.opponent_id,
			last_message_id: this.last_message_id,
            has_countdown:  $( "#esd_countdown" ).attr('value'),
            is_esd_session: this.is_esd_session
		}

		if (this.msg_entries.length) {
			params.msg_entries = this.msg_entries;
			this.msg_entries = [];
		}

		this.ajaxCall('ping', params, {
			complete: this.delegates.pingComplete
		});
	},

	/**
	 * Immediately stops pinging.
	 */
	stop: function(error_message) {
		window.clearInterval(this.ping_interval);
		this.ping_queued = false;
		this.ping_in_process = true; // hack

		if (error_message) {
			var fl_box = SK_alert(error_message);
			fl_box.$body.find('a')
				.click(function() {
					window.opener.open(this.href);
					return false;
				});
			fl_box.bind('close', function() {window.close()});
		}

		this.$input.disable();
		this.$send_btn.disable();
	},

	/**
	 * Server-called callback.
	 */
	drawMessages: function(msg_list)
	{
		var $tpl_msg = this.$('#message_tpl');

       	if(this.$enable_sound)
		{
            var im_player = new SWFObject( this.swf_player_src, "im_sound_player_embed", 100, 25, "9" );
			im_player.addParam("allowfullscreen","false");
			im_player.addVariable("file", SITE_URL+"static/sound/receive.mp3");
            im_player.addVariable("autostart", 'yes');

			im_player.write("im_sound_player");

            //$("#im_sound_player").html("<embed id='im_embed' name='im_embed' type='audio/x-wav' src='"+SITE_URL+"im_msg.wav' hidden='true' autostart='true' ></embed>" );
    	}

		for (var i = 0, msg, $msg; msg = msg_list[i]; i++) {
			if (parseInt(this.last_message_id) >= parseInt(msg.im_message_id)) {
				continue;
			}

			$msg = $tpl_msg.clone().removeAttr('id');

			$msg.addClass((msg.sender_id != this.opponent_id) ? 'my_msg' : 'opp_msg');

			$msg.children('.msg_time').text(msg.format_time);
			$msg.children('.msg_username')
                .text(msg.sender_name+':')
                .bind("click", {href: msg.href}, function(params){
                    window.open(params.data.href);
                    return false;
                });

			$msg.children('.msg_text').html(msg.text);
			$msg.children('.msg_text').css('color', '#'+msg.color);

			this.$msg_list.append($msg);

			this.last_message_id = msg.im_message_id;
		}

		this.$window.scrollTop(this.$msg_list.height());
	},

    refreshCountdown: function(sec)
    {
        $( "#esd_countdown" ).attr('value', sec);
    }

});
