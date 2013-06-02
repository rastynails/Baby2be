function component_AddComment(auto_id)
{
	this.DOMConstruct('AddComment', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_AddComment.prototype =
	new SK_ComponentHandler({
		
		construct : function(){
		
		},
		
		commentListRepaint: function( html ){
			this.$('#comment_list_cont').empty().append(html);
		}
		
	});