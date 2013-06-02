
function component_Chat(auto_id)
{
	this.DOMConstruct('Chat', auto_id);

	var handler = this;

	this.msg_entries = [];

	this.$('#chat_input_form')
		.submit(function() {
			var $input = handler.$('#chat_input');
			var value = jQuery.trim($input.val());
			if (value) {
				var color_txt = handler.$('#chat_win_msg_prototype .msg_text').css('color');
				var color = handler.palette_picker.color || handler.palette_picker.rgbToHex(color_txt);
				var msg = {
						text: value,
						color: color
				};
				handler.msg_entries.push(msg);
			}

			$input.val('');

			return false;
		});

	// delegate functions
	this.delegates = {
		selectRoom: function(data) {
			return handler.selectRoom(data.room_id);
		}
	}
}

component_Chat.prototype =
	new SK_ComponentHandler({

	construct: function(ping_interval, owner_id) {
		this.owner_id = owner_id;

                var handler = this;

                this.pingCommand = SK_Ping.getInstance().addCommand('chat', {
                    params: {
                        room_id: null,
                        userlist_hash: null,
                        last_message_id: null
                    },
                    before: function()
                    {
                        var $room = handler.rooms[handler.active_room_id];

                        this.params.room_id = handler.active_room_id;
                        this.params.userlist_hash = $room.userlist_hash;
                        this.params.last_message_id = $room.last_message_id;

                        this.params.msg_entries = [];

                        if (handler.msg_entries.length) {
                            this.params.msg_entries = handler.msg_entries;
                            handler.msg_entries = [];
                        }
                    },
                    after: function( res )
                    {
                        if ( res.js )
                        {
                            (new Function(res.js)).call(handler);
                        }
                    }
                });

                this.pingCommand.start(ping_interval);
	},

	/**
	 * Draws the rooms accordion.
	 */
	drawRooms: function(room_list)
	{
		this.rooms = {};

		var $rooms_container = this.$('#chat_rooms_container');
		var $tpl_block = this.$block('#chat_room_prototype');

		$rooms_container.empty();

		for (var i = 0, room, $room; room = room_list[i]; i++)
		{
			$room = $tpl_block.clone().removeAttr('id').appendTo($rooms_container);
			$room.$title.text(room.name);

			$room.$window = this.$('#chat_room_window_prototype').clone().removeAttr('id');
			this.$('#chat_windows_container').append($room.$window);

			$room.last_message_id = 0;
			$room.userlist_hash = '';

			$room.bind('click', {room_id: room.chat_room_id}, this.delegates.selectRoom);

			this.rooms[room.chat_room_id] = $room;
		}
	},

	drawRoomsUsersCounter: function(count_list) {
		for (var room_id in count_list) {
			this.rooms[room_id].$title
				.siblings('.users_count')
				.text('('+count_list[room_id]+')');
		}
	},

	selectRoom: function(room_id, expand)
	{
		if (expand) {
			this.rooms[room_id].expand(false);
		}

		if (this.active_room_id == room_id) {
			return false;
		}

		if (this.active_room_id) {
			this.rooms[this.active_room_id]
				.removeClass('block-active')
				.collapse(false)
				.$window.hide();
		}

		var $room = this.rooms[room_id];

		$room.addClass('block-active');

		this.$('#chat_windows_container').scrollTop(
			$room.$window.css('display', '').height()
		);

		this.$('#active_window_title .block_cap_title')
			.text(this.rooms[room_id].$title.text());

		this.active_room_id = room_id;

                this.pingCommand.start();

		return true;
	},

	drawRoomUsers: function(room_id, room_users, userlist_hash)
	{

		var $tpl_user = this.$('#chat_room_user_prototype');

		var $room = this.rooms[room_id];
		var $userlist = $room.find('.chat_room_users');

		$userlist.empty();

		for (var i = 0, user, $user; user = room_users[i]; i++ )
		{
			$user = $tpl_user.clone()
					.removeAttr('id')
					.addClass('sex_ico-'+user.sex);

			$user.children('.chat_room_user')
				.text(user.username)
				.bind('click', {profile_id: user.profile_id},
					function(event) {
						if(typeof(window.is_123wm) != 'undefined' )
							return;
						SK_openIM(event.data.profile_id);
						return false;
					});
			if ( this.owner_id==user.profile_id ) {
				$user.children('.chat_room_user').unbind("click");
			}

			$userlist.append($user);
		}

		$room.userlist_hash = userlist_hash;
	},


	drawMessages: function(room_id, msg_list)
	{
		var $tpl_msg = this.$('#chat_win_msg_prototype');
		var $room = this.rooms[room_id];

		for (var i = 0, msg, $msg; msg = msg_list[i]; i++) {
			if (parseInt($room.last_message_id) >= parseInt(msg.chat_message_id)) {
				continue;
			}

			$msg = $tpl_msg.clone().removeAttr('id');

			$msg.children('.msg_time').text(msg.format_time);
			$msg.children('.msg_username')
                .text(msg.username+':')
                .bind("click", {href: msg.href}, function(params){
                    window.open(params.data.href);
                    return false;
                });

			$msg.children('.msg_text').html(msg.text);
			$msg.children('.msg_text').css('color', '#'+msg.color);

			$room.$window.append($msg);

			$room.last_message_id = msg.chat_message_id;
		}

		this.$('#chat_windows_container').scrollTop($room.$window.height());
	},

	/**
	 * Immediately stops pinging.
	 */
	stop: function(error_message) {
		if (error_message) {
                    var fl_box = SK_alert(error_message);
		}

		this.$('#chat_input').disable();
		this.$('#send_btn').disable();

                this.pingCommand.stop();
	}

});
