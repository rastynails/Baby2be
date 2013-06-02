function component_BlogIndexList(auto_id)
{
	this.DOMConstruct('BlogIndexList', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
	};
}

component_BlogIndexList.prototype =
	new SK_ComponentHandler({
		
		construct : function(){
			var handler = this;
            var $latest = this.$('a[rel='+this.auto_id+'latest]');
            var $popular = this.$('a[rel='+this.auto_id+'popular]');
            $latest.click(function(){
                $latest.addClass('active');
                $latest.parent().addClass('active');
                $popular.removeClass('active');
                $popular.parent().removeClass('active');
                handler.$('#popular_cont').hide();
				handler.$('#latest_cont').show();
            });
            
            $popular.click(function(){
                $popular.addClass('active');
                $popular.parent().addClass('active');
                $latest.removeClass('active');
                $latest.parent().removeClass('active');
                handler.$('#latest_cont').hide();
				handler.$('#popular_cont').show();
            });
		}
	});