function component_ForumSearch(auto_id)
{
	this.DOMConstruct('ForumSearch',auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_ForumSearch.prototype =
	new SK_ComponentHandler({
	
	search_box : undefined,
	
	mode : "",
	
	construct : function(){
		var handler = this;		
		handler.$("#search_btn").bind('click', function(){
			handler.showSearchBox();
		});			
	},
		
	showSearchBox : function(){		
		this.search_box = new SK_FloatBox({
			$title		: this.$(".forum_search_title"),
			$contents	: this.$(".forum_search_content"),
			width		: 350/*,
			position 	: { top : 250, left : 330}*/
			
		});		
	},
	
	
	hideSearchBox: function() {
		this.search_box.close();
	},
	
	redirect: function(url) {
		if ( url == undefined ) 
			window.location.reload();
		else 
			window.location.href = url;
	}
	
});

