function component_ProfileNote(auto_id)
{
	this.DOMConstruct('ProfileNote', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_ProfileNote.prototype =
	new SK_ComponentHandler({
	

	construct : function(event_id, profile_id, viewer_id){
		
		var handler = this;

		this.profile_id = profile_id;
		this.viewer_id = viewer_id;
		
		var $delete_title = this.$("#delete_title").text();
		var $delete_msg = this.$("#delete_msg").text();
		
		var $form_cont = this.$("#edit_note_form");
		
        
		this.send_data = {};
		
		this.send_data.profile_id = profile_id;
        this.send_data.event_id = event_id;
        
	
		handler.$('#esd_reference_bookmark').bind( 'click', function(){
            
            handler.send_data.note = $("#note").attr('value');
			handler.ajaxCall('ajax_BookmarkHandler', handler.send_data );
		});
		
		handler.$('#esd_profile_note_close').bind( 'click', function(){
			window.close();
		});

	},

    close: function(){
        window.close();
        //alert('close');
    }

	
});

