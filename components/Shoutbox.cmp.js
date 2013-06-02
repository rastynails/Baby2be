
function component_Shoutbox(auto_id)
{
	this.DOMConstruct('Shoutbox', auto_id);

	this.$window = this.$('#window');
	this.$msg_list = this.$('#msg_list');
	this.$sidebar = this.$('#sidebar');
	this.$bottom = this.$('#bottom');
	this.$input = this.$('#input');
        this.$username = this.$('#username');
	this.$send_btn = this.$('#send_btn');

        this.$lastMessageId = 0;
	var handler = this;

	this.$('#shoutbox_input_form')
		.submit(function() {
			var value = jQuery.trim(handler.$input.val());
			if (value && ( value != handler.labelMessage)) {
				//var color_txt = $('body').css('color');
				var color = handler.palette_picker.color || "00aaff"; //handler.palette_picker.rgbToHex(color_txt);
                var name = jQuery.trim(handler.$username.val());

                var msg = {
                        username: name,
                        text: value,
                        color: color
                };
                handler.ajaxCall('addMessage', msg);

                handler.$input.val('');
			}
            else
            {
                handler.$input.focus();
            }



			return false;
		});

}

component_Shoutbox.prototype =
	new SK_ComponentHandler({

	/**
	 * Custom constructor.
	 */
	construct: function(ping_interval, labelMessage)
	{
            var handler = this;

            this.labelMessage = labelMessage;

            SK_Ping.getInstance().addCommand('shoutbox', {
                params: {
                    labelMessage: handler.labelMessage,
                    lastMessageId: handler.$lastMessageId
                },
                before: function()
                {
                    this.params.labelMessage = handler.labelMessage;
                    this.params.lastMessageId = handler.$lastMessageId;
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

    removeMessage: function(messageId)
    {
        $('#'+messageId).remove();
    },

	/**
	 * Server-called callback.
	 */
	drawMessages: function(lastMessageId, msg_list)
	{
            var handler = this;

            if ($('.shoutbox_preloader').length > 0)
                $('.shoutbox_preloader').remove();

            if ($('div#no_messages_label').length > 0)
                $('div#no_messages_label').remove();

            handler.$lastMessageId = lastMessageId;
            var $tpl_msg = this.$('#message_tpl');

            for (var i = 0, msg, $msg; msg = msg_list[i]; i++) {
                $msg = $tpl_msg.clone().removeAttr('id');

                $msg.attr('id', 'message_'+msg.id);
                $msg.find('.profile_thumb').attr('src', msg.profile_thumb_url);
                $msg.find('.msg_username').text(msg.username).attr('href', msg.href);
                $msg.find('.msg_time').text(msg.time);
                $msg.find('.msg_text').html(msg.text);
                $msg.find('.msg_text').css('color', '#'+msg.color);
                $msg.find('.profile_thumb_wrapper').attr('href', msg.href);
                $msg.find('.profile_thumb').attr('title', msg.username);
                $msg.find('.shoutbox_delete').attr('id', 'delete_'+msg.id);
                $msg.find('.shoutbox_delete').click(function(){
                    var messageId = $(this).attr('id');
                    handler.ajaxCall("deleteMessage", {id:messageId});
                });

                this.$msg_list.append($msg);
            }

            this.$window.scrollTop(this.$msg_list.height());
	}

});
