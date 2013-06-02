function component_Rate(auto_id)
{
	this.DOMConstruct('Rate', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_Rate.prototype =
	new SK_ComponentHandler({

		construct : function( param, mode ){

			this.mode = mode;
			
			this.userRate = param.score;
			
			this.items = param.items;
			
			this.entity_id = param.entity_id;
			
			this.feature = param.feature;
			
			var handler = this;
			
			this.setUserRate( param.score );
			
			$.each( param.items, function( index, data ){
				handler.$('#' + data.id).bind( 'mouseover', function(){
					handler.setUserRate( (index+1) )
				} ).bind( 'mouseout', function(){
					handler.setUserRate( handler.userRate )
				} ).bind( 'click', function(){
					if( handler.mode ){
						handler.updateUserRate(index+1);
					}
					else{
						handler.error( handler.$('#no_rate').html(), 'error' );
					}
				} );
			} );
			
		},
		
		setUserRate : function( rate ){
			
			var handler = this;
			
			$.each( handler.items, function( index, data ){
					handler.$( '#' + data.id ).removeClass( 'active' );						
			} );
			
			$.each( handler.items, function( index, data ){
				if( handler.userRate != null && (index+1) <= rate )
					handler.$( '#' + data.id ).addClass( 'active' );						
			} );
		},
	
		rateSuccess: null,
		
		updateUserRate : function( rate ) {
			
			var handler = this;
			
			if( rate == this.userRate )
				return;
			this.$('#user_rate').empty().append(rate);	
			this.userRate = rate;
			var children = this.children;
			
			this.ajaxCall('ajax_updateRate',
				{score:rate, feature:this.feature, entity_id:this.entity_id},
				{success: function() {
					
				if( handler.rateSuccess != null ){
					handler.rateSuccess();
					return;
				}
				
				for (var i = 0, child; child = children[i]; i++) {
					if (child instanceof component_RateTotal) {
						child.reload({entity_id:handler.entity_id, feature:handler.feature});
						//break;
					}
				}
			}});
		}
		
	});