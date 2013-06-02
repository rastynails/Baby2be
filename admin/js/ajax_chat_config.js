
jQuery(function() {
	window.sk_chat_config_handler = new (function()
	{
		// history config form
		this.$history_form = jQuery('#chat_history_config_form');

		var $history_by_time_elements =
			jQuery('[name=chat_history_time_digit], [name=chat_history_time_unit]');

		var $history_by_msgs_elements =
			jQuery('[name=recent_msgs_num]');

		var $history_submit = jQuery('#chat_history_config_form_submit');

		jQuery('[name=chat_history_type]')
			.click(function() {
				if (this.value == 'by_time') {
					$history_by_msgs_elements.attr('disabled', 'disabled');
					$history_by_time_elements.removeAttr('disabled');
				}
				else {
					$history_by_time_elements.attr('disabled', 'disabled');
					$history_by_msgs_elements.removeAttr('disabled');
				}
			})
			.removeAttr('disabled')
			.filter('[checked]').click();

		jQuery('[name=chat_history_type], [name=chat_history_time_unit]')
			.one('change', function() {
				$history_submit.removeAttr('disabled');
			});

		jQuery('[name=recent_msgs_num], [name=chat_history_time_digit]')
			.keyup(function() {
				if (this.value != this.defaultValue) {
					$history_submit.removeAttr('disabled');
				}
			});


		// create room form
		jQuery('#create_new_room_tbl input[type=text]')
			.keyup(function() {
				if (lang_check_values('.lang_input_room_name', '', false)) {
					jQuery('#create_chat_room_btn').removeAttr('disabled');
				}
				else {
					jQuery('#create_chat_room_btn').attr('disabled', 'disabled');
				}
			});

		// rooms config form
		jQuery('#chat_rooms_tbl .chat_room_checkbox')
			.one('change', function() {
				jQuery('#chat_rooms_save_btn').removeAttr('disabled');
			});

		// rooms delete handling
		jQuery('#chat_rooms_tbl .delete')
			.click(function()
			{
				var room_id = /\#delete\((\d+)\)$/.exec(this.href)[1];
				var room_name = $jq('#chat_room_name-'+room_id+' a').text();

				if ( !confirm('Do you really want to delete room "'+room_name+'"?') ) {
					return false;
				}

				jQuery(
					'<form method="post">'+
						'<input type="hidden" name="action" value="delete_room" />'+
						'<input type="hidden" name="room_id" value="'+room_id+'" />'+
						'<input type="submit" style="display: none" />'+
					'</form>'
				)
				.appendTo('body')
				.submit();

				return false;
			});
	});
});
