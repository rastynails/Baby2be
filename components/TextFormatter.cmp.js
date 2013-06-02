function component_TextFormatter(auto_id)
{
	this.DOMConstruct('TextFormatter', auto_id);

	var self = this;

	this.$target;
	this.mode = 'normal';

	this.delegates = {
		bold: function(selection){
			self.addTag({tag: 'b'});
		},
		italic: function(){
			self.addTag({tag: 'i'});
		},
		underline: function(){
			self.addTag({tag: 'u'});
		},
		quote: function(){
			if (this.type="bbtag") {
				self.addTag({tag: 'quote'});
			} else {
				self.addTag({tag: 'blockquote'});
			}
		},
		link: function() {
			self.suggestLink(function(url, label) {

				if (self.mode == 'bbtag')
				{
					self.addText('[url="' + url + '"]' + label + '[/url]');
				}
				else
				{
					self.addTag({tag: 'a', attrs: {href: url}, content: label});
				}

			}, self.$target.selection());
		},

		emoticon: function(){
			self.suggestSmile(function(smile){
				self.addText(smile);
			});
		},

		image: function(){
			self.suggestImage(function(src, title){
				if (this.mode == 'bbtag')
				{
					self.addTag({
						tag: 'img',
						attrs: {
							title: title
						},
						content: src,
						pare: true
					});
				}
				else
				{
					self.addTag({
						tag: 'img',
						attrs: {
							src: src,
							title: title
						},
						pare: false
					});
				}
			});
		}
	};
}

component_TextFormatter.prototype =
	new SK_ComponentHandler({

		construct : function(targetId, mode) {
			var self = this;
			this.$target = $('#' + targetId);

			this.mode = mode || 'normal';

			this.$('.b_control').click(function(){
				var command = $(this).attr('sk-tf-command');
				if (!command || typeof self.delegates[command] != 'function') {
					return false;
				}

				self.delegates[command].apply(this);

				return false;
			});

		},

		addTag: function(options) {
			var attrsStr = '',
			content = options.content || this.$target.selection();
			attrs = options.attrs || {},
			tag = options.tag || '',
			tagString = '',
			pare = true;

			if ( typeof options.pare != 'undefined' &&  options.pare !== null ) {
				pare = options.pare;
			}

			$.each(attrs, function(name, value){
				var foo = name + '="' + value + '"';
				foo = ' ' + foo;
				attrsStr = attrsStr + foo;
			});

			if (this.mode == 'bbtag') {
				tagString = pare ? "[" + tag + attrsStr + "]" + content + "[/" + tag + "]" : "[" + tag + attrsStr + "]";

			} else {
				tagString = pare ? "<" + tag + attrsStr + ">" + content + "</" + tag + ">" : "<" + tag + attrsStr + " />";
			}

			this.$target.selection(tagString);
		},

		addText: function(text) {
			var content = this.$target.selection()
			this.$target.selection(content + text);
		},

		suggestLink: function(callback, title) {
			var handler = this, selection = this.$target.selection(),
				$c = $('#tf_link_suggest'),
				$content = $c.find(".content"),
				$form = $content.find('form.link_form');

			$($form.get(0).title).val(title);

			var box = new SK_FloatBox( {
				$title		: $c.find(".title"),
				$contents	: $content,
				width		: 400
			});

			$form.unbind().submit(function(){

				var url = $(this.url).val(), label = $(this.title).val();

				if ( !$.trim(url) ) {
					$(this.url).focus();
					SK_drawError( SK_Language.text('interface.text_formatter.message.incorrect_url') );
					return false;
				}
				if ( !$.trim(label) ) {
					$(this.title).focus();
					SK_drawError( SK_Language.text('interface.text_formatter.message.incorrect_title') );
					return false;
				}

				callback(url, label);
				box.close();

				return false;
			});
		},

		suggestSmile: function(callback) {
			var handler = this,
				$c = this.$('#smile-box-c');

			if ( typeof window.sk_active_smile_set != 'undefined' && window.sk_active_smile_set !== null ) {
				$c.append(window.sk_active_smile_set);
				window.sk_active_smile_set = null;

				return;
			}

			var $box = this.$('#smile-box');
			window.sk_active_smile_set = $box;
			$box.find('.smile').unbind();

			if ( this.thickbox == undefined ) {
				this.thickbox = this.$(".b_emoticon").parents(".floatbox_container").length;
			}

			if ( !this.thickbox ) {
				$box.css( {position: 'absolute'} );
			}

			var $smileBtn = this.$(".b_emoticon");

			var position = $smileBtn.offset();
			var _p = {top: position.top - 208, left: position.left + 2};
			_p.left = (_p.left + 408) > $(window).width() ? _p.left - 387 : _p.left;

			$box.css(_p).prependTo("body");

			$box.find('.smile').click(function(){
                            callback($(this).find('img').attr("alt"));
			});

			window.setTimeout(function(){
				$("body").one("click.hide_smile", function() {
					$c.append($box);
					window.sk_active_smile_set = null;
				});
			}, 100);
		},

		suggestImage: function(callback) {
			SK_EventManager.trigger('tf.suggestImage', {callback: callback, entity: this.entity, mode: this.mode, $target: this.target});
		}

});