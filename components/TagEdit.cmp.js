function component_TagEdit(auto_id)
{
	this.DOMConstruct('TagEdit', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_TagEdit.prototype =
	new SK_ComponentHandler({
		
		construct : function() {
		},
		
		tagListRepaint: function( html ) {
			this.$('#tag_list').empty().append(html);
		}
		
	});