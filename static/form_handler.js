/**
 * The prototype for form front-end handler.
 */
/**
 * Constructor.
 * @param object obj_prototype
 */
function SK_FormHandler(obj_prototype)
{
	var key;
	for (key in obj_prototype) {
		this[key] = obj_prototype[key];
	}
}

SK_FormHandler.prototype =
{
	DOMConstruct: function(form_class, auto_id)
	{
		this.form_class = form_class;
		this.auto_id = auto_id;

		this.events = {
			submit: [],
			success: [],
			error: [],
			complete: []
		}

		this.$form = jQuery('#'+auto_id);

		// collecting fields
		var fields_list = {};
			regex = /^([\w\-]+)(\[([^\]]+)?\])?/i,
			elem_list = this.$form.get(0).elements,
			name;
		for (var i = 0, elem; elem = elem_list[i]; i++)
		{
			if (!elem.name)
				continue;

			name = regex.exec(elem.name)[1];

			if (!fields_list[name]) {
				fields_list[name] = $(elem);
			}
			else {
				fields_list[name].add(elem);
			}
		}

		// instantiating fields
		for (name in this.fields) {
			if (fields_list[name]) {
				var fieldAutoId = this.auto_id + '-' + name;
				this.fields[name].construct(fields_list[name], this, fieldAutoId);
			}
		}
		// handling an actions
		var handler = this;

		if (this.default_action) {
			this.$form.submit(function(event) {
				handler.submit(event, handler.default_action);
				return false;
			});
		}

		var $btn;
		for (name in this.actions)
		{
			$btn = $('#'+auto_id+'-'+name+'-button');

			if (!$btn.length ||
				($btn.get(0).type == 'submit' && this.default_action)
			) {
				continue;
			}

			$btn.bind('click', {action: name}, function(event) {
				handler.submit(event, event.data.action);
			});
		}
	},


	/**
     * @return jQuery
     */
	$: function(selector, context) {
		selector = selector.replace('#', '#'+this.auto_id+'-');
		context = context || this.container_node;
		return jQuery(selector, context);
	},


	collectData: function(action_name)
	{
		var form_data = this.$form.formToArray();
		var action = this.actions[action_name];

		var data = {};
		var regex = /^([\w\-]+)(\[([^\]]+)?\])?(\[([^\]]+)?\])?/i;
		var name, match, key;

		for (var i = 0, item; item = form_data[i]; i++)
		{
			// parsing html name
			match = regex.exec(item.name);
			name = match[1];
			item.value = item.value || '';

			if (typeof action.fields[name] == 'undefined') {
				continue;
			}

			if (match[2]) {
				if (match[2] == '[]') {
					if (data[name] === undefined) {
						data[name] = [];
					}
					data[name].push(item.value);
				}
				else if (match[3]) {
					key = match[3];
					if (typeof data[name] == 'undefined') {
						data[name] = {length: 0};
					}

					data[name].length++;

					//second brackets
					if (match[4] == '[]') {
						if (data[name][key] === undefined) {
							data[name][key] = [];
						}
						data[name][key].push(item.value);
					}
					else if (match[5]) {
						var sub_key = match[5];
						if (typeof data[name][key] == 'undefined') {
							data[name][key] = {length: 0};
						}
						data[name][key][sub_key] = item.value;
					} else {
						data[name][key] = item.value;
					}

				}
			}
			else { // if there are no brackets
				data[name] = item.value;
			}
		}

		var errors = [];
		var field, required;

		for (key in action.fields)
		{
			required = action.fields[key];
			field = this.fields[key];
			var fieldAutoId = this.auto_id + '-' + key;
                        var checkValue = typeof data[key] === 'string' ? $.trim(data[key]) : data[key];

			if ( checkValue && checkValue.length !== 0 )
                        {
				try {
					field.validate(data[key], required, fieldAutoId);
				} catch (e) {
					var err_msg = SK_Language.text(
						'$forms.'+this.name+'.fields.'+key+'.errors.required'
						, {label: SK_Language.text('$forms.'+this.name+'.fields.'+key)}
					);
					errors.push({msg: err_msg, key: key});
				}
			}
			else if (required) {
				var err_msg = SK_Language.text(
					'$forms.'+this.name+'.fields.'+key+'.errors.required'
					, {label: SK_Language.text('$forms.'+this.name+'.fields.'+key)}
				);
				errors.push({msg: err_msg, key: key});
			}

			if (data[key] && data[key].length !== undefined) {
				delete(data[key]['length']);
			}

			if (errors.length == 1) {
				field.focus();
			}


		}

		if (errors.length) {
			for (var i = errors.length-1, err; err = errors[i]; i--) {
				this.error(err.msg, err.key, i == 0);
			}
			return false;
		}
		return data;
	},


	submit: function(event, action_name, confirmed, data)
	{
		confirmed = confirmed || false;

		if (!confirmed) {
			$('.macos_msg_node').remove();
			$('.form_field_error', this.$form)
				.next('br').remove()
				.andSelf().remove();
		}

		data = data || this.collectData(action_name);

		if (data)
		{
			this.disable();
			this.$('#'+action_name+'-button')
				.add(this.$form).addClass('in_process');

			var handler = this;

			if (this.actions[action_name].confirm_msg && !confirmed)
			{
				SK_confirm(
					'<span>'+SK_Language.text(this.actions[action_name].confirm_msg)+'</span>'
					, function() {
						handler.submit(event, action_name, true, data);
					}
				).bind('close', function() {
					handler.enable();
					handler.$('#'+action_name+'-button')
						.add(handler.$form).removeClass('in_process');
				});

				return false;
			}

			if (this.file_upload_in_process)
			{
				var int_pointer = window.setInterval(function() {
					if (!handler.file_upload_in_process) {
						window.clearInterval(int_pointer);
						handler.submit(event, action_name, confirmed, data);
					}
				}, 300);
			}

			this.trigger('submit');

			jQuery.ajax({
				url: URL_FORM_PROCESSOR,
				type: 'POST',
				data: {
					form: this.name,
					action: action_name,
					data: encodeURIComponent( JSON.stringify(data) )
				},
				dataType: 'json',
				success: function(response)
				{
					if (response.debug_vars) {
						for (var i = 0, dbg_var; dbg_var = response.debug_vars[i]; i++) {
							console.debug(dbg_var);
						}
					}

					if (response.errors)
					{
						for (var i = 0, error; error = response.errors[i]; i++) {
							if (error.constructor == Array) {
								handler.error(error[0], error[1], i == 0);
							}
							else {
								handler.error(error);
							}
						}

						handler.trigger('error');
					}

					if (response.exec) {
                                            (new Function(response.exec)).call(handler);
					}

					if (response.messages) {
						for (var i = 0, msg; msg = response.messages[i]; i++) {
							handler.message(msg);
						}
					}

					if (!response.errors) {
						handler.trigger('success', [response.data]);
					}
				},

				complete: function(response) {
					handler.$('#'+action_name+'-button').add(handler.$form).removeClass('in_process');
					handler.enable();
					handler.trigger('complete', [response.data]);
				},

				error: function(xhr, textStatus, errorThrown) {

					if (xhr.responseText) {
						SK_alert(xhr.responseText);
					}
					else {
						handler.error('There is an error occurred during the post form');
					}

					// handling backend exception
					var json_e = xhr.getResponseHeader('SK-Exception');
					if (json_e) {
						eval("var e = "+json_e);
						e.toString = function() {
							return e.message+"\ntrace: "+e.trace_str+"\nfile: "+e.file+" in line: "+e.line;
						}
						// throw e;
						SK_alert('<div>'+nl2br(e.toString())+'</div>');
					}
				}
			});
		}
	},


	disable: function() {
		jQuery(this.$form.get(0).elements).attr('disabled', 'disabled');
	},

	enable: function() {
		jQuery(this.$form.get(0).elements).removeAttr('disabled');
	},


	bind: function(type, func) {
		if (this.events[type] == undefined) {
			throw 'undefined form event type "'+type+'"';
		}

		this.events[type].push(func);
	},

	trigger: function(type, params) {
		if (this.events[type] == undefined) {
			throw 'undefined form event type "'+type+'"';
		}

		params = params || [];

		for (var i = 0, func; func = this.events[type][i]; i++) {
			if (func.apply(this, params) === false) {
				return false;
			}
		}
	},

	clearErrors : function( field_key )
	{
		var $context = this.$form;
		if ( field_key )
		{
			$context = this.$('#'+field_key+'-container');
		}

		$('.form_field_error', $context).next('br').andSelf().remove();
	},

	error: function(err_msg, field_key, focus) {

		if (field_key) {
			var $container = this.$('#'+field_key+'-container');
			$container.append(
				'<div class="form_field_error" style="display: none"></div><br clear="all" />'
			)
			.children('.form_field_error').html(err_msg).fadeIn('fast');
			var $field = this.$('#'+field_key);
			var $_field;

			if ($field.is('input, select, textarea')) {
				$field.attr('disabled', false);
				$_field = $field;
			} else {
				$_field = $('input:eq(0), select:eq(0), textarea:eq(0)', $field.get(0)).attr('disabled', false);
			}

			if ( focus )
			{
				$_field.focus();
			}
		}
		else {
			SK_drawError(err_msg, -1);
		}
	},


	message: function(msg_text, type) {
		SK_drawMessage(msg_text, type, -1);
	}

}


function SK_FormFieldValidationException(message)
{
	this.message = message;

	this.toString = function() {
		return err_msg;
	}
}
