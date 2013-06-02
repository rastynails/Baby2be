function component_AlbumControl(auto_id)
{
	this.DOMConstruct('AlbumControl', auto_id);
}

component_AlbumControl.prototype =
	new SK_ComponentHandler({

		construct : function(album_id){
			var handler = this;
			this.album_id = album_id;
			
			this.controls = {
				$save: this.$('.controls .save'),
				$remove: this.$('.controls .delete'),
				$edit: this.$('.controls .edit')
			};
			
			//Binding Events
			
			this.controls.$remove.click(function(){
				handler.showDeleteConfirm();
			});
			
			this.controls.$edit.click(function(){
				if (!handler.moving) {
					handler.openForm();
				}
			});
			
			this.controls.$save.click(function(){
				handler.save();
			});
			
			this.$('.cancel_edditing').click(function(){
				handler.closeForm();
			});
			
			this.$('.album_privacy').change(function(){
				handler.onChangePrivacy($(this).val())
			});
		},
		
		onChangePrivacy: function( val )
		{
			if ( val == 'password_protected' )
			{
				this.$('.password_c').show();
			}
			else
			{
				this.$('.password_c').hide();
			}
		},
		
		openForm: function() {
			this.$('.album_details_cont').hide();
			this.$('.album_edit_cont').show();
			
			this.onChangePrivacy(this.$('.album_privacy').val());
		},
		
		closeForm: function() {
			this.$('.album_edit_cont').hide();
			this.$('.album_details_cont').show();
		},
		
		showDeleteConfirm: function() {
			var handler = this;
			var $content = this.$('#fb_content');
			SK_confirm($content, function(){
				handler.remove();
			});
		},
		
		remove: function(){
			this.ajaxCall('ajax_Remove', {album_id: this.album_id});
		},
		
		redirect: function(url) {
			if (url) {
				window.location.href = url;
			} else {
				window.location.reload(true);
			}
		}
		
	});