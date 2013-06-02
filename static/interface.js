
// disabling/enabling
jQuery.fn.extend({
	disable: function() {
		this.each(function()
		{
			this.disabled = true;

			if ( this.tagName != 'INPUT' && this.tagName != 'TEXTAREA' && this.tagName != 'SELECT' )
			{
				this.jQuery_disabled_clone =
					jQuery(this)
					.addClass('disabled')
					.clone()
					.removeAttr('id')
					.get(0);

				jQuery(this)
					.hide()
					.bind('unload', function(){
						jQuery(this).enable();
					})
					.after(this.jQuery_disabled_clone);
			}
		});

		return this;
	},

	enable: function()
	{
		this.each(function()
		{
			this.disabled = false;

			if ( this.jQuery_disabled_clone )
			{
				jQuery(this.jQuery_disabled_clone).remove();
				this.jQuery_disabled_clone = null;

				jQuery(this)
					.unbind('unload', function(){
						jQuery(this).enable();
					})
					.removeClass('disabled')
					.show();
			}
		});

		return this;
	}
});


function SK_Exception(message, code)
{
	this.toString = function() {
		return message;
	}

	this.getCode = function() {
		return code;
	}
}

function SK_drawError(err_msg, delay) {
	SK_drawMessage(err_msg, 'error', delay);
}

function SK_drawMessage(msg_text, type, delay)
{
	type = type || 'message';
	delay = delay || (1000*10);

	if (SK_drawMessage.in_process) {
		if (msg_text) {
			SK_drawMessage.queue.unshift([msg_text, type, delay]);
		}
		return;
	}

	if (!msg_text) {
		var item = SK_drawMessage.queue.shift();
		if (!item) {
			return;
		}
		msg_text = item[0];
		type = item[1];
		delay = item[2];
	}

	SK_drawMessage.in_process = true;

	// getting draw position
	var $last = jQuery('.macos_msg_node:last');
	var top_pos = (!$last.length) ? 0 : $last.position().top + $last.outerHeight() + 2;
	var $msg_block =
		// creating message block
		jQuery('<div class="macos_msg_node macos_'+type+'" style="display: none"></div>')
			.appendTo('body')
			.html(msg_text)
			.prepend('<a class="close_btn" href="#"></a>')
			.css('top', top_pos)
			.fadeTo(50, 0.1, function() {
				jQuery(this).css('display', '');
				SK_drawMessage.in_process = false;
				SK_drawMessage();
				jQuery(this).fadeTo(300, 1, function() {

					if (delay > 0) {
						window.setTimeout(function() {
							try {
								$msg_block.fadeOut(2500, function() {
									jQuery(this).remove();
								});
							} catch (e) {}
						}, delay);
					}
				});
			});

	$msg_block.children('.close_btn')
		.click(function() {
			jQuery(this).parent().fadeOut(100, function() {
				jQuery(this).remove();
			});
			return false;
		}
	);

}
SK_drawMessage.in_process = false;
SK_drawMessage.queue = [];




/**
 * Float box constructor.
 *
 * @param string|jQuery $title
 * @param string|jQuery $contents
 * @param jQuery $controls
 * @param object position {top, left} = center
 * @param integer width = auto
 * @param integer height = auto
 */
function SK_FloatBox(options)
{
    this.parentFB = null;

    if ( window.SK_ActiveFloatBox )
    {
        this.parentFB = window.SK_ActiveFloatBox;
    }

    window.SK_ActiveFloatBox = this;

    var fb_class;

	if (typeof document.body.style.maxHeight === 'undefined') { //if IE 6
		jQuery('body').css({height: '100%', width: '100%'});
		jQuery('html').css('overflow', 'hidden');
		if (document.getElementById('floatbox_HideSelect') === null) { //iframe to hide select elements in ie6
			jQuery('body').append('<iframe id="floatbox_HideSelect"></iframe><div id="floatbox_overlay"></div>');
			fb_class = SK_FloatBox.detectMacXFF() ? 'floatbox_overlayMacFFBGHack' : 'floatbox_overlayBG';
			jQuery('#floatbox_overlay').addClass(fb_class);
		}
	}
	else { //all others
		if (document.getElementById('floatbox_overlay') === null) {
			jQuery('body').append('<div id="floatbox_overlay"></div>');
			fb_class = SK_FloatBox.detectMacXFF() ? 'floatbox_overlayMacFFBGHack' : 'floatbox_overlayBG';
			jQuery('#floatbox_overlay').addClass(fb_class);
		}
	}

	jQuery('body').css('overflow', 'hidden');

	this.$container = jQuery('.floatbox_container', '#sk-floatbox-block-prototype').clone().appendTo('body');

	this.$header = this.$container.find('.block_cap_title');

	if (typeof options.$title == 'string') {
		options.$title = jQuery('<span>'+options.$title+'</span>');
	}
	else {
		this.$title_parent = options.$title.parent();
	}

	this.$header.append(options.$title);

	this.$body = this.$container.find('.block_body_c');

	if (typeof options.$contents == 'string') {
		options.$contents = jQuery('<span>'+options.$contents+'</span>');
	}
	else {
		this.$contents_parent = options.$contents.parent();
	}

	this.$body.append(options.$contents);

	this.$bottom = this.$container.find('.block_bottom_c');

	if (options.$controls) {
		if (typeof options.$controls == 'string') {
			options.$controls = jQuery('<span>'+options.$controls+'</span>');
		}
		else {
			this.$controls_parent = options.$controls.parent();
		}

		this.$bottom.append(options.$controls);
	}

	if (options.width)
		this.$container.css("width", options.width);
	if (options.height)
		this.$container.css("height", options.height);

	var fl_box = this;
	jQuery('.close_btn', this.$container.find('.floatbox_header'))
		.one('click', function() {
			fl_box.close();
			return false;
		});

	this.esc_listener =
	function(event) {
		if (event.keyCode == 27) {
			fl_box.close();
			return false;
		}
		return true;
	}

	jQuery(document).bind('keydown', this.esc_listener);

	this.$container
		.fadeTo(1, 0.1, function()
		{
			var $this = jQuery(this);

			$this.css('display', 'block');

			if (options.position) {
				$this.css(options.position);
			}
			else {
				var position = {
					top:((jQuery(window).height()/2) - ($this.height()/2))/*.ceil()*/,
					left:((jQuery(window).width()/2) - ($this.width()/2))/*.ceil()*/
				};

				$this.css(position);
			}

			// trigger on show event
			fl_box.trigger('show');

			$this.fadeTo(100, 1);
		});

	this.events = {close: [], show: []};
}

SK_FloatBox.detectMacXFF = function()
{
	var userAgent = navigator.userAgent.toLowerCase();
	return (userAgent.indexOf('mac') != -1 && userAgent.indexOf('firefox') != -1);
}

SK_FloatBox.prototype = {
	close: function()
	{
		if (this.trigger('close') === false) {
			return false;
		}

		jQuery(document).unbind('keydown', this.esc_listener);

		if (this.$title_parent && this.$title_parent.length) {
			this.$title_parent.append(
				this.$header.children()
			);
		}
		if (this.$contents_parent && this.$contents_parent.length) {
			this.$contents_parent.append(this.$body.children());
		}
		if (this.$controls_parent && this.$controls_parent.length) {
			this.$controls_parent.append(this.$bottom.children());
		}

		this.$container.remove();

		if (jQuery('.floatbox_container:not("#sk-floatbox-block-prototype .floatbox_container")').length === 0) {
			jQuery('body').css('overflow', '');
                        if ( !this.parentFB )
                        {
                            jQuery('#floatbox_overlay, #floatbox_HideSelect').remove();
                        }
		}

                if ( window.SK_ActiveFloatBox == this )
                {
                    window.SK_ActiveFloatBox = null;
                }

                return true;
	},

	bind: function(type, func)
	{
		if (this.events[type] == undefined) {
			throw 'form error: unknown event type "'+type+'"';
		}

		this.events[type].push(func);

	},

	trigger: function(type, params)
	{
		if (this.events[type] == undefined) {
			throw 'form error: unknown event type "'+type+'"';
		}

		params = params || [];

		for (var i = 0, func; func = this.events[type][i]; i++) {
			if (func.apply(this, params) === false) {
				return false;
			}
		}

		return true;
	}
}


function SK_alert($title, $contents, callback)
{
	if (!callback &&
		typeof $contents == 'function' &&
		$contents.constructor != Array) {
		callback = $contents;
	}

	if (!$contents || $contents == callback) {
		$contents = $title;
		$title = SK_Language.text('%interface.alert_title');
	}

	var $ok_btn =
		jQuery('<input type="button" />')
		.val(SK_Language.text('%interface.ok'));

	var fl_box = new SK_FloatBox({
		$title: $title,
		$contents: $contents,
		$controls: $ok_btn
	});

	$ok_btn.one('click', function() {
		fl_box.close();
		if (callback) {
			callback.apply(fl_box);
		}
	});

	return fl_box;
}


function SK_confirm($title, $contents, callback)
{
	if (!callback &&
		typeof $contents == 'function' &&
		$contents.constructor != Array) {
		callback = $contents;
	}

	if (!$contents || $contents == callback) {
		$contents = $title;
		$title = SK_Language.text('%interface.confirmation_title');
	}

	var $ok_btn =
		jQuery('<input type="button" />')
		.val(SK_Language.text('%interface.ok'));

	var $cancel_btn =
		jQuery('<input type="button" />')
		.val(SK_Language.text('%interface.cancel'));

	var fl_box = new SK_FloatBox({
		$title: $title,
		$contents: $contents,
		$controls: jQuery($ok_btn)
					.add('<span>&nbsp;</span>')
					.add($cancel_btn)
	});

	$ok_btn.one('click', function() {
		fl_box.close();
		if (callback) {
			callback.apply(fl_box);
		}
	});

	$cancel_btn.one('click', function() {
		fl_box.close();
	});

	return fl_box;
}



function SK_BlockHandler(block_node)
{
	this.$block = jQuery(block_node);

	this.$block_cap =
		jQuery('.block_cap:eq(0)', this.$block);

	this.$title =
		jQuery('.block_cap_title:eq(0)', this.$block_cap);

	this.$body =
		jQuery('.block_body:eq(0)', this.$block);

	this.$expand_btn =
		jQuery('.block_expand:eq(0), .block_collapse:eq(0)', this.$block_cap);

	this.events = {
		click: [],
		expand: [],
		collapse: []
	}

	var handler = this;

	this.$block_cap
		.click(function() {
			if (handler.$expand_btn.hasClass('block_expand')) {
				handler.expand();
			}
			else if (handler.$expand_btn.hasClass('block_collapse')) {
				handler.collapse();
			}
			return false;
		});
}

SK_BlockHandler.prototype = {

	expand: function(trigger_events)
	{
		if (trigger_events === undefined) {
			trigger_events = true;
		}

		if (!trigger_events || (
			this.trigger('expand') !== false
			&& this.trigger('click') !== false)
		) {
			this.$expand_btn.prop('class', 'block_collapse');
			this.$body.slideDown('fast');
		}

		return this;
	},

	collapse: function(trigger_events)
	{
		if (trigger_events === undefined) {
			trigger_events = true;
		}

		if (!trigger_events || (
			this.trigger('collapse') !== false
			&& this.trigger('click') !== false)
		) {
			this.$expand_btn.prop('class', 'block_expand');
			this.$body.slideUp('fast');
		}

		return this;
	},

	show: function(speed, callback) {
		this.$block.show(speed, callback);
	},

	hide: function(speed, callback) {
		this.$block.hide(speed, callback);
	},

	bind: function(type, arg1, arg2)
	{
		if (this.events[type] == undefined) {
			throw 'block error: unknown event type "'+type+'"';
		}

		if (!arg2) {
			this.events[type].push([arg1]);
		}
		else {
			this.events[type].push([arg1, arg2]);
		}
	},

	trigger: function(type)
	{
		if (this.events[type] == undefined) {
			throw 'block error: unknown event type "'+type+'"';
		}

		for (var i = 0, item; item = this.events[type][i]; i++)
		{
			if (item.length == 1) {
				if (item[0].apply(this) === false) {
					return false;
				}
			}
			else if (item[1].call(this, item[0]) === false) {
				return false;
			}
		}

		return true;
	},

	clone: function(clone_e)
	{
		var $clone = this.$block.clone();
		var node = $clone.get(0);

		node.sk_block_handler = new SK_BlockHandler(node);

		if (clone_e) {
			node.sk_block_handler.events = this.events;
		}

		return node.sk_block_handler;
	},

	append: function(content) {
		return this.$body.append(content);
		return this;
	},

	appendTo: function(content) {
		this.$block.appendTo(content);
		return this;
	},

	empty: function() {
		this.$body.empty();
		return this;
	},

	children: function(expr) {
		return this.$body.children(expr).not('.block_body_corner');
	},

	find: function(expr) {
		return this.$body.find(expr).not('.block_body_corner');
	},

	removeAttr: function(name) {
		this.$block.removeAttr(name);
		return this;
	},

	addClass: function(cls) {
		this.$block.addClass(cls);
		return this;
	},

	removeClass: function(cls) {
		this.$block.removeClass(cls);
		return this;
	}
}

jQuery(function() {
	jQuery('.block_expand, .block_collapse')
		.each(function() {
			var block_node = this.parentNode.parentNode.parentNode.parentNode;
			if (jQuery(block_node).hasClass('block')) {
				block_node.sk_block_handler = new SK_BlockHandler(block_node);
			}
		});
});



SK_Language = {

	data: {},

	text: function(lang_addr, var_list)
	{
		if ( SK_Language.data[lang_addr] === undefined ) {
			throw new SK_Exception('language section ['+lang_addr+'] not found');
		}

		var text = SK_Language.data[lang_addr];

		if (var_list) {
			for ( key in var_list ) {
				text = text.replace('{$'+key+'}', var_list[key]);
			}
		}

		return text;
	}
}

function nl2br(str) {
	return (str + '').replace(/([^>]?)\n/g, '$1<br />\n');
}

function SK_SignIn() {
	return window.sk_component_sign_in.showBox();
}

function SK_openIM(opponent_id, is_esd)
{
	if (is_esd)
		is_esd_session = '&is_esd_session='+is_esd;
	else
		is_esd_session = '';
	return window.open(
		URL_MEMBER+'im.php?opponent_id='+opponent_id+is_esd_session,
		'im_with_'+opponent_id,
		'width='+SK_openIM.width+','+
		'height='+SK_openIM.height+','+
		'resizable=yes, location=no, scrollbars=no, status=no'
	);
}
SK_openIM.width = 445;
SK_openIM.height = 460;

function SK_profileNote( event_id, opponent_id)
{
	return window.open(
		URL_MEMBER+'profile_note.php?event_id='+event_id+'&opponent_id='+opponent_id,
		'note_'+opponent_id,
		'width='+SK_profileNote.width+','+
		'height='+SK_profileNote.height+','+
		'resizable=yes, location=no, scrollbars=no, status=no'
	);
}
SK_profileNote.width = 325;
SK_profileNote.height = 425;

SK_EventManager = {

	events: {},

	bind: function(event, callback)
	{
		if ( typeof this.events[event] == 'undefined' || this.events[event] === null)
		{
			this.events[event] = [];
		}

		this.events[event].push(callback);
	},

	trigger: function(event, eventObject)
	{
		if (typeof this.events[event] != 'undefined' && this.events[event] !== null)
		{
			for (var i = 0; i < this.events[event].length; i++)
			{
				if (typeof this.events[event][i] == 'function')
				{
					if (this.events[event][i](eventObject) === false)
					{
						return;
					}
				}
			}
		}
	}
};

SK_SetFieldInvitation = function(id, label)
{
	var $node = $('#' + id);
	$node.addClass('input_invitation').val(label);

	$node.focus(function() {
		var v = $node.val();
		$node.removeClass('input_invitation');

		if ( v == label )
		{
			$node.val('');
		}
	}).blur(function() {
		var v = $node.val();
		if ( !v )
		{
			$node.addClass('input_invitation').val(label);
		}
	});

	$($node.get(0).form).submit(function(){
		if ( $node.val() != label )
		{
			$node.removeClass('input_invitation');
		}
	});
}