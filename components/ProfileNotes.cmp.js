function component_ProfileNotes(auto_id)
{
	this.DOMConstruct('ProfileNotes', auto_id);
	
	var handler = this;
	
	this.delegates = {
		
	};
}

component_ProfileNotes.prototype =
	new SK_ComponentHandler({
	
	construct: function(profile_id, viewer_id) {
	
		var handler = this;
		
		this.profile_id = profile_id;
		this.viewer_id = viewer_id;
				
		var $delete_btn = this.$("#del_note");
		var $delete_title = this.$("#delete_title").text();
		var $delete_msg = this.$("#delete_msg").text();
		
		var $form_cont = this.$("#edit_note_form");
		var $note_cont = this.$("#note_text");
		
		var $edit_btn = this.$("#edit_note");
		
		var $cancel_btn = this.$("#cancel_btn");
		
		var $add_btn = this.$("#add_note_control");
		
		$delete_btn.click(function(){
			SK_confirm($delete_title, $delete_msg, function() {
				handler.ajaxCall("ajax_DeleteNote", {profile_id: handler.profile_id, viewer_id: handler.viewer_id});
			});
		});
		
		$edit_btn.click(function(){
			$(this).css('display','none');
			$note_cont.fadeOut('fast', function(){
				$form_cont.fadeIn('fast');
			});
		});		
		
		$cancel_btn.click(function(){
			$form_cont.fadeOut('fast', function(){
				$note_cont.fadeIn('fast');
				$edit_btn.css('display','block');
			});
		});
		
		$add_btn.click(function(){
			$note_cont.fadeOut('fast', function(){
				$form_cont.fadeIn('fast');
			});
		});
	},
	
	hideForm: function(note) {
	
		var $form_cont = this.$("#edit_note_form");
		var $note_cont = this.$("#note_text");
		
		var handler = this;
		
		$form_cont.fadeOut('fast', function() {
			
			$note_cont.fadeIn('fast'); 
			$note_cont.text(note);
			handler.$("#edit_note").css('display','block');
		});
	}
});

