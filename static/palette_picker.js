function palettePicker( target_id ) {
	
	this.id = target_id;
	
	this.$target_id = $("#" + this.id);
	
	this.construct();
}

palettePicker.prototype =
{
	construct: function() {
		var handler = this;
		this.$colors_block = this.$(".palette_picker");
				
		this.$target_id.bind("click", function() {
			handler.showColorBox();
		});
		this.$colors_block.find(".palette_col").bind("mouseover", function(){
			handler.setBGcolor( $(this).attr("title") );
		}).bind("click", function(){
			handler.setColor( $(this).attr("title") );
			handler.$(".palette_picker").unbind("mouseout");
			handler.$colors_block.hide();
		});							
	}, 
	
    /**
     * @return jQuery
     */
	$: function(selector, context) {
		var context = context || jQuery("#pc-" + this.id).get(0);
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
		
		this.$(".palette_picker").bind("mouseout", function(){
			handler.setBGcolor(handler.color);
		});		
	},

	bindBody: function() {
		var handler = this;
		$("body").unbind("click.hide_color");
		window.setTimeout(function(){
			$("body").one("click.hide_color", function() {
				handler.$colors_block.hide();
				handler.setBGcolor(handler.color);
			});
		}, 100);		
	},
	
	setColor: function( color ) {
		this.color = color;
	},
	
	/**
	 * Sets btn background-color
	 * @param color
	 */
	setBGcolor: function( color ) {
		if (color)
		{
			this.$target_id.css("background-color", "#"+color);
		}
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

