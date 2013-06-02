function component_ProfileStatus(auto_id)
{
	this.DOMConstruct('ProfileStatus', auto_id);
	
	var handler = this;
	
	this.delegates = {
		toggle_profile_status: function() {
			handler.ajaxCall('toggleProfileStatus', {curr_status: handler.profile_status});
			return false;
		}
	};
}

component_ProfileStatus.prototype =
	new SK_ComponentHandler({
		
		profile_status: null,
		
		display: function(toggle_btn_label, profile_status_label, profile_status)
		{
			var $toggle_cont =
				this.$('#profile_status_toggle_cont')
				.empty();
			
			var $toggle_btn =
				$('<a href="#"></a>')
				.text(toggle_btn_label)
				.click(this.delegates.toggle_profile_status)
				.appendTo($toggle_cont);
			
			this.$('#status_value').text(profile_status_label);
			
			this.profile_status = profile_status;
		}
	});