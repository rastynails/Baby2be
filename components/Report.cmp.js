function component_Report(auto_id)
{
	this.DOMConstruct('Report', auto_id);
	
	var handler = this;
	
	this.delegates = {
		
	};
}

component_Report.prototype =
	new SK_ComponentHandler({
	
	construct: function(f_title){
			
		var handler = this;
		
		this.title = f_title;
						
		var $button_show = this.$('#button_show');
		var $report_cont = this.$('#report_cont').children();
		
		$button_show.click(function() {
			window.report_fl_box = new SK_FloatBox({
				$title: handler.title,
				$contents: $report_cont,
				width: 350
			});
			
			jQuery("#video_player_cont").css("display", "none");
			window.report_fl_box.bind('close', function(){
				jQuery("#video_player_cont").css("display", "");
			});            		
		});
	}
});
