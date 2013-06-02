(function($){
	
	$.fn.tooltip = function($content) {
		var tt = new tooltip(this);
		
		if ($content!=undefined) {
			tt.construct($content);
		}
		return this;
	}
	
	var tooltip = function($node) {
		this.$node = $node;
	}
	
	tooltip.prototype = {
		
		$container: {},
		$content_placeholder_node: {},
		$content: {},
		
		construct: function($content) {
			var handler = this;
			this.$container = $("<div>").css({position: "absolute", display: "none"}).addClass("tooltip");
			
			this.$content_placeholder_node = $("<div>").css("display", "none")
			
			this.$content = $content;
			
			//$content.replaceWith(this.$content_placeholder_node);
			
			$("body").append(this.$container);
			
			this.$node.unbind("mouseover").mouseover(function(e){
				handler.show();
			})
			
			this.$node.unbind("mouseout").mouseout(function(e){
				handler.hide();
			})
		},
		
		show: function() {
			this.$container.append(this.$content.clone());
			var handler = this;
			
			this.$container.show();
			this.$node.unbind("mousemove").bind("mousemove", function(e){
				handler.$container.css({top: e.pageY, left: e.pageX +10});
			});
		},
		
		hide: function() {
			this.$node.unbind("mousemove");
			this.$container.hide();
			this.$container.empty();
		}
	}
	
})(jQuery)