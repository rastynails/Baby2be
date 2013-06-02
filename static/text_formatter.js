function textFormatter(input_id, mode, error_msg) {
	
	this.id = input_id;
	
	this.input_text = $("#" + this.id);
	
	this.forum_mode = mode;
	
	this.error_msg = error_msg;	
	
	this.construct();
}

textFormatter.prototype =
{
	construct: function() {
		var handler = this;
		this.link_url   = this.$("#add_link_url");
		this.link_title = this.$("#add_link_title");
		this.$smile_block = this.$(".add_smile_block");
				
		this.$(".b_bold").bind("click", function() {
			handler.addTag("b");
		});
		this.$(".b_italic").bind("click", function() {
			handler.addTag("i");
		});		
		this.$(".b_underline").bind("click", function() {
			handler.addTag("u");
		});		
		this.$(".b_quote" ).bind("click", function() {
			handler.addTag("quote");
		});	
		this.$(".b_link").bind("click", function() {
			handler.showLinkBox();
		});	
		this.$(".b_emoicon").bind("click", function() {
			handler.showSmileBox();
		});
		this.$(".add_smile_block .smile").bind("click", function(){
			handler.addSmile( $(this).children("img").attr("alt") );
		});
		
		this.$(".b_image").bind("click", function() {
			window.showImages();
		});
		
		this.$smile_block.appendTo($('body'));
		
	}, 
	
    /**
     * @return jQuery
     */
	$: function(selector, context) {
		var context = context || jQuery("#tf-" + this.id).get(0);
		return jQuery(selector, context);
	},	
	
	showLinkBox: function() {			
		var handler = this;		
		var $button     = this.$("#add_link_button");
		
		this.link_box = new SK_FloatBox( {
			$title		: this.$(".add_link_title").text(),
			$contents	: this.$(".add_link_content"),
			width		: 400
		});
		$button.unbind("click");
		$button.bind("click", function() {
			handler.checkLinkData();
		});
	},
	
	showSmileBox: function() {	
		var handler = this;
		if ( this.$smile_block.is(":visible") ) {
			this.$smile_block.hide();
			return;
		}
		if ( this.thickbox==undefined ) {
			this.thickbox = this.$(".b_emoicon").parents(".floatbox_container").length;
		}
		if ( !this.thickbox ) {
			this.$smile_block.css( {position: 'absolute'} );
		}
		
		var position = this.$(".b_emoicon").offset({ margin: false, scroll: false, relativeTo: document.window });
		var _p = {top: position.top-206, left: position.left+2};
		_p.left = (_p.left + 408) > $(window).width() ? _p.left - 387 : _p.left;
		
		this.$smile_block.css(_p).show();
		this.bindBody();
	},	
	
	addTag: function ($tag) {
		var $sel_text = this.input_text.selection();
		if (this.forum_mode) {
			this.input_text.selection("[" + $tag + "]" + $sel_text + "[/" + $tag + "]");
		}
		else {
			this.input_text.selection("<" + $tag + ">" + $sel_text + "</" + $tag + ">");
		}
	},
	
	addLink: function($url, $title) {	
		var $sel_text = this.input_text.selection();
		if (this.forum_mode) {
			this.input_text.selection("[url='" + $url + "']" + $title + "[/url]");
		}
		else {
			this.input_text.selection('<a href="' + $url + '">' + $title + '</a>');
		}			
	},	
	
	addSmile: function($code) {
		var $sel_text = this.input_text.selection();
		this.input_text.selection($code);	
		this.$smile_block.hide();
	},	
	
	checkLinkData: function () {	
		if ( !$.trim(this.link_url.val() ) ) {
			SK_drawError( SK_Language.text('interface.text_formatter.message.incorrect_url') );
			return;
		}
		if ( !$.trim(this.link_title.val()) ) {
			SK_drawError( SK_Language.text('interface.text_formatter.message.incorrect_title') );
			return;
		}		
		this.link_box.close();
		this.addLink(this.link_url.val(), this.link_title.val());		
	},
	
	bindBody: function() {
		var handler = this;
		$("body").unbind("click.hide_smile");
		window.setTimeout(function(){
			$("body").one("click.hide_smile", function() {
				handler.$smile_block.hide();
			});
		}, 100);		
	}
}

