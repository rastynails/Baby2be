function smileSet(input_id) {
	
	this.id = input_id;
	
	this.$input_text = $("#" + this.id);
	
	this.construct();
}

smileSet.prototype =
{
	construct: function() {
		var handler = this;
		this.$smile_block = this.$(".add_smile_block");
				
		this.$(".chat_smile").bind("click", function() {
			handler.showSmileBox();
		});
		this.$(".add_smile_block .smile").bind("click", function(){
			handler.addSmile( $(this).children("img").attr("alt") );
		});		
	}, 
	
    /**
     * @return jQuery
     */
	$: function(selector, context) {
		var context = context || jQuery("#cs-" + this.id).get(0);
		return jQuery(selector, context);
	},	
	
	showSmileBox: function() {	
		var handler = this;
		if ( this.$smile_block.is(":visible") ) {
			this.$smile_block.hide();
			return;
		}		
		this.$smile_block.show();
		this.bindBody();
	},	
	
	addSmile: function(code) {
		this.$input_text.selection(code);	
		this.$smile_block.hide();
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

