window.color_pickers = window.color_pickers || {}; 

function colorPicker( name, color ) {
	
	this.$container = jQuery('pc-' + name);
	
	this.$target_id = this.$('#id-' + name);
	
	this.$input = this.$('input:text[name='+name+']');
	
	this.construct();
	
	if (color != undefined) {
		this.setColor(color);
		this.setBGcolor(color);
	}
}

colorPicker.prototype =
{
	construct: function() {
		var handler = this;
		this.$colors_block = this.$(".color_picker");
				
		this.$target_id.bind("click", function() {
			handler.showColorBox();
		});
		this.$(".color_picker .color_col").bind("mouseover", function(){
			handler.setBGcolor( jQuery(this).attr("title") );
		});
		this.$(".color_picker .color_col").bind("click", function(){
			handler.setColor( jQuery(this).attr("title") );
			handler.$(".color_picker").unbind("mouseout");
			handler.$colors_block.hide();
		});	
		
		this.$input.keyup(function(){
			var color = jQuery.trim(jQuery(this).val());
			handler.setColor(color);
			handler.setBGcolor(color);
		});
	}, 
	
    /**
     * @return jQuery
     */
	$: function(selector, context) {
		var context = context || this.$container.get(0);
		return jQuery(selector, context);
	},
	
	showColorBox: function() {	
		var handler = this;
		if ( this.$colors_block.is(":visible") ) {
			this.$colors_block.hide();
			return;
		}		
		this.$colors_block.show();		
		this.bindBody();
		
		this.$(".color_picker").bind("mouseout", function(){
			handler.setBGcolor(handler.color);
		});		
	},

	bindBody: function() {
		var handler = this;
		jQuery("body").unbind("click.hide_color");
		window.setTimeout(function(){
			jQuery("body").one("click.hide_color", function() {
				handler.$colors_block.hide();
				handler.setBGcolor(handler.color);
			});
		}, 100);		
	},
	
	setColor: function( color ) {
		this.color = color;
		this.$input.val(color);
	},
	
	/**
	 * Sets btn background-color
	 * @param color
	 */
	setBGcolor: function( color ) {
		this.$input.val(color);
		color = color || '000';
		this.$target_id.css("background-color", "#"+color);
	},
	
	rgbToHex: function(color_txt)
	{
		var decToHex="";
		var arr = new Array();
		
		arr = /rgb\((\d+),\s?(\d+),\s?(\d+)\)/.exec(color_txt);
		if ( !arr ) { 
			return color_txt.substr(1); 
		}
		
		for(var i=1;i<4;i++)
		{
			var hexArray = new Array( "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F" );
			var code1 = Math.floor(arr[i] / 16);
			var code2 = arr[i] - code1 * 16;
			decToHex += hexArray[code1];
			decToHex += hexArray[code2];
		}
		return (decToHex);
	}	

}

