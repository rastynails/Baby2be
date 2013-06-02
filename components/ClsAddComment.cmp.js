function component_ClsAddComment(auto_id)
{
	this.DOMConstruct('ClsAddComment', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_ClsAddComment.prototype =
	new SK_ComponentHandler({
		
		construct : function(){
		
		},
		
		commentListRepaint: function( html ){
			this.$('#comment_list_cont').empty().append(html);
		}
		
	});