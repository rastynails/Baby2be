function component_EventIndexCalendar(auto_id)
{
	this.DOMConstruct('EventIndexCalendar', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
	};
}

component_EventIndexCalendar.prototype =
	new SK_ComponentHandler({
		construct : function(){
			var handler = this;

            var $calendar = this.$('a[rel='+this.auto_id+'calendar]');
            var $list = this.$('a[rel='+this.auto_id+'events]');
            
            $calendar.click(function(){
                $calendar.addClass('active');
                $calendar.parent().addClass('active');
                $list.removeClass('active');
                $list.parent().removeClass('active');
                handler.$('#list_cont').hide();
				handler.$('#calendar_cont').show();
            });

            $list.click(function(){
                $list.addClass('active');
                $list.parent().addClass('active');
                $calendar.removeClass('active');
                $calendar.parent().removeClass('active');
                handler.$('#calendar_cont').hide();
				handler.$('#list_cont').show();
            });
			
		}
		
		
	});